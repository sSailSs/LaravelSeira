<?php

namespace App\State;

use ApiPlatform\Laravel\Eloquent\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as SupportCollection;

/**
 * @implements ProcessorInterface<SchoolClass, SchoolClass>
 */
class SchoolClassProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly PersistProcessor $persistProcessor,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof SchoolClass) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        [$studentsProvided, $studentsInput] = $this->extractStudentsInput($data, $context);

        // Prevent API Platform from trying to persist students as a DB column.
        $this->clearTransientStudentsState($data);

        $persisted = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        if ($studentsProvided && $persisted instanceof SchoolClass) {
            $persisted->students()->sync($this->normalizeStudentIds($studentsInput));
            $persisted->load('students');
        }

        return $persisted;
    }

    /**
     * @return array{0: bool, 1: mixed}
     */
    private function extractStudentsInput(SchoolClass $data, array $context): array
    {
        $request = $context['request'] ?? null;
        if ($request instanceof Request && $request->exists('students')) {
            return [true, $request->input('students')];
        }

        $attributes = $data->getAttributes();
        if (array_key_exists('students', $attributes)) {
            return [true, $attributes['students']];
        }

        if ($data->relationLoaded('students')) {
            return [true, $data->getRelation('students')];
        }

        return [false, null];
    }

    private function clearTransientStudentsState(SchoolClass $data): void
    {
        $data->offsetUnset('students');
        $data->unsetRelation('students');
    }

    /**
     * @return list<int>
     */
    private function normalizeStudentIds(mixed $students): array
    {
        if ($students instanceof EloquentCollection || $students instanceof SupportCollection) {
            $students = $students->all();
        }

        if (!is_array($students)) {
            $students = null === $students ? [] : [$students];
        }

        $ids = [];

        foreach ($students as $student) {
            $id = $this->extractStudentId($student);
            if (null !== $id) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    private function extractStudentId(mixed $student): ?int
    {
        if ($student instanceof User) {
            return (int) $student->getKey();
        }

        if (is_int($student) || (is_string($student) && ctype_digit($student))) {
            return (int) $student;
        }

        if (is_string($student)) {
            if (preg_match('~/(\\d+)$~', trim($student), $matches)) {
                return (int) $matches[1];
            }

            return null;
        }

        if (is_array($student)) {
            if (isset($student['id']) && is_numeric($student['id'])) {
                return (int) $student['id'];
            }

            if (isset($student['@id']) && is_string($student['@id'])) {
                return $this->extractStudentId($student['@id']);
            }
        }

        return null;
    }
}
