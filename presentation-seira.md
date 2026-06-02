---
marp: true
theme: default
paginate: true
size: 16:9
style: |
  section {
    font-family: Arial, Helvetica, sans-serif;
  }
  h1 {
    color: #8c3c1d;
  }
  h2 {
    color: #2a2520;
  }
  code {
    font-size: 0.78em;
  }
  pre {
    border-radius: 8px;
  }
  table {
    font-size: 0.78em;
  }
---

# Seira

## Plateforme e-learning scolaire

Projet Laravel / DevOps  
Presentation de 20 a 30 minutes

---

# Objectif de la presentation

Montrer comment nous avons construit une plateforme scolaire complete :

- un besoin fonctionnel clair,
- une architecture Laravel structuree,
- une base de donnees relationnelle,
- des droits par role,
- une API REST,
- des tests automatises,
- une logique de deploiement.

---

# Plan

1. Presentation du projet
2. Cadrage / organisation
3. Conception fonctionnelle
4. Architecture technique
5. Base de donnees
6. Developpement
7. Securite
8. Tests
9. Deploiement
10. Bilan

---

# 1. Presentation du projet

## C'est quoi Seira ?

Seira est une plateforme de gestion pedagogique pour un etablissement scolaire.

Elle permet a trois profils de travailler dans un meme environnement :

| Role | Besoin |
|---|---|
| Admin | Superviser la plateforme |
| Professeur | Creer et organiser les cours |
| Eleve | Consulter les cours et suivre sa progression |

---

# Probleme traite

Avant une plateforme centralisee :

- les supports sont disperses,
- le suivi des eleves est manuel,
- les videos et documents sont difficiles a organiser,
- les roles ne sont pas toujours bien separes.

Avec Seira :

- tout est rattache a une classe,
- les cours sont structures,
- les droits sont controles,
- la progression est sauvegardee.

---

# Demonstration du domaine

```text
Classe : 6A
  Cours : Mathematiques - 6A
    Chapitre : Introduction
      Contenu : Video de cours
      Contenu : Evaluation rapide
    Chapitre : Exercices
      Contenu : Video de correction
```

Cette hierarchie se retrouve directement dans le code et la base de donnees.

---

# Maquette : espace eleve

```text
+-------------------------------------------------------------+
| Seira                         Mes cours        Profil       |
+----------------------+--------------------------------------+
| Progression globale  | Mathematiques - 6A                   |
| 68 %                 | [Video] Introduction                 |
|                      | [Texte] Exercices corriges           |
| Mes classes          | [Video] Evaluation rapide            |
| - 6A                 |                                      |
| - Groupe soutien     | Francais - 6A                        |
|                      | [Texte] Lecture                      |
+----------------------+--------------------------------------+
```

Objectif : permettre a l'eleve de retrouver rapidement ses cours et sa progression.

---

# Maquette : espace professeur

```text
+-------------------------------------------------------------+
| Seira                   Mes matieres      Deconnexion        |
+----------------------+--------------------------------------+
| Classes              | Mathematiques - 6A                   |
| - 6A                 |                                      |
| - 5B                 | Sequence pedagogique                 |
|                      |  Module : Introduction               |
| Actions              |    1. Video de cours                 |
| + Creer un cours     |    2. Evaluation rapide              |
| + Ajouter fichier    |                                      |
|                      |  [+ Ajouter une video] [+ Module]    |
+----------------------+--------------------------------------+
```

Objectif : donner au professeur une vue claire pour organiser ses contenus.

---

# 2. Cadrage / organisation

## Perimetre realise

- Authentification
- Gestion des roles
- Classes et inscriptions
- Cours, chapitres et contenus
- Progression eleve
- Upload de fichiers
- API REST documentee
- Policies de securite
- Events / listeners
- Tests automatises

---

# Organisation technique

Le projet est organise par responsabilites :

```text
app/
  Models/        Entites Eloquent
  Policies/      Regles d'autorisation
  Events/        Evenements metier
  Listeners/     Reactions aux evenements
  Observers/     Surveillance des modeles
  State/         Logique API specifique
  Http/
    Controllers/ Controleurs web

database/
  migrations/    Structure SQL
  seeders/       Donnees de demonstration
  factories/     Donnees de test
```

---

# 3. Conception fonctionnelle

## Cas d'utilisation principaux

| Acteur | Cas d'utilisation |
|---|---|
| Admin | Gerer les utilisateurs, classes et cours |
| Professeur | Creer un cours, ajouter chapitres et videos |
| Eleve | Suivre un cours et enregistrer sa progression |

Le systeme est pense autour d'un parcours simple : creer, publier, consulter, progresser.

---

# Parcours eleve

```text
Connexion
  -> Espace eleve
    -> Mes cours
      -> Ouvrir un cours
        -> Lire une video
          -> Enregistrer la progression
```

Exemple concret :

Un eleve regarde 5 minutes sur une video de 10 minutes.  
La progression est sauvegardee dans `user_content_progress`.

---

# Parcours professeur

```text
Connexion
  -> Espace professeur
    -> Mes matieres
      -> Creer un cours
        -> Ajouter un module
          -> Ajouter une video
```

Le professeur gere la structure pedagogique sans manipuler directement la base.

---

# 4. Architecture technique

## Stack

| Couche | Technologie |
|---|---|
| Backend | Laravel / PHP |
| ORM | Eloquent |
| API | API Platform |
| Front | Blade, Tailwind, Alpine.js |
| Build front | Vite |
| Tests | PHPUnit |
| Base | SQL via migrations Laravel |

---

# Architecture applicative

```text
Utilisateur
   |
   v
Routes web / API
   |
   v
Controller ou API Platform
   |
   v
Policy + Model Eloquent
   |
   v
Base de donnees
```

En complement : observers, events et listeners pour les actions importantes.

---

# Schema : architecture Laravel

```text
                  +------------------+
                  |   Navigateur     |
                  +--------+---------+
                           |
                           v
        +------------------+------------------+
        | Routes web / routes API             |
        +------------------+------------------+
                           |
             +-------------+-------------+
             |                           |
             v                           v
   +-------------------+       +-------------------+
   | Controllers Blade |       | API Platform      |
   +---------+---------+       +---------+---------+
             |                           |
             +-------------+-------------+
                           v
                 +---------+---------+
                 | Policies + Models |
                 +---------+---------+
                           |
                           v
                 +---------+---------+
                 | Base de donnees   |
                 +-------------------+
```

Ce schema montre la separation entre interface web, API, securite et persistance.

---

# Exemple : inclusion des assets

Dans le layout Sirae, les assets front sont charges avec Vite :

```php
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

En developpement :

```bash
npm run dev
```

En production :

```bash
npm run build
```

---

# Exemple : routes web

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard']);
    Route::get('/space/admin', [LearningSpaceController::class, 'admin']);
    Route::get('/space/prof', [LearningSpaceController::class, 'prof']);
    Route::get('/space/eleve', [LearningSpaceController::class, 'eleve']);
});
```

Ces routes permettent de separer les espaces selon les roles.

---

# Exemple : API REST

Les modeles sont exposes avec API Platform :

```php
#[ApiResource(operations: [
    new GetCollection(),
    new Post(rules: [
        'title' => 'required|string|max:255',
        'school_class_id' => 'required|integer|exists:school_classes,id',
    ]),
    new Get(),
    new Patch(),
    new Delete(),
])]
class Course extends Model
{
    // ...
}
```

L'API est documentee sur `/api/docs`.

---

# Exemple : endpoint API

Creation d'un cours via API :

```http
POST /api/courses
Content-Type: application/json
```

```json
{
  "title": "Mathematiques - 6A",
  "description": "Programme annuel de mathematiques",
  "school_class_id": 1,
  "teacher_id": 2
}
```

La validation est portee par les regles declarees dans le modele.

---

# 5. Base de donnees

## Tables principales

```text
users
school_classes
class_user
courses
chapters
chapter_contents
user_content_progress
file_metadata
books
```

Les fichiers `mcp-sirae.mmd` et `mpd-sirae.puml` contiennent les schemas.

---

# Schema : MCD simplifie

```text
USER
  | 0..*
  | suit
  v
USER_CONTENT_PROGRESS
  ^
  | 0..*
  |
CHAPTER_CONTENT
  ^
  | 0..*
CHAPTER
  ^
  | 0..*
COURSE
  ^
  | 0..*
SCHOOL_CLASS
```

Ce schema presente la logique metier avant le detail des tables physiques.

---

# Schema : MPD simplifie

```text
USERS (id PK)
  1 -------- 0..* COURSES (teacher_id FK)
  1 -------- 0..* CLASS_USER (user_id FK)

SCHOOL_CLASSES (id PK)
  1 -------- 0..* COURSES (school_class_id FK)
  1 -------- 0..* CLASS_USER (school_class_id FK)

COURSES
  1 -------- 0..* CHAPTERS

CHAPTERS
  1 -------- 0..* CHAPTER_CONTENTS

CHAPTER_CONTENTS
  1 -------- 0..* USER_CONTENT_PROGRESS
```

La version complete est dans `mpd-sirae.puml`.

---

# Extrait de migration

Creation de la table des cours :

```php
Schema::create('courses', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->foreignId('school_class_id')
        ->constrained('school_classes')
        ->cascadeOnDelete();
    $table->foreignId('teacher_id')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();
    $table->timestamps();
});
```

---

# Relations principales

```text
User 0..1 -> 0..* SchoolClass
User 0..1 -> 0..* Course
SchoolClass 1 -> 0..* Course
Course 1 -> 0..* Chapter
Chapter 1 -> 0..* ChapterContent
User 1 -> 0..* UserContentProgress
ChapterContent 1 -> 0..* UserContentProgress
```

Le MPD montre les cardinalites de chaque cote des relations.

---

# Exemple : relation Eloquent

Dans `Course`, un cours appartient a une classe et a un professeur :

```php
public function schoolClass(): BelongsTo
{
    return $this->belongsTo(SchoolClass::class);
}

public function teacher(): BelongsTo
{
    return $this->belongsTo(User::class, 'teacher_id');
}

public function chapters(): HasMany
{
    return $this->hasMany(Chapter::class);
}
```

---

# Code : modele User

Extrait de logique de role :

```php
public const ROLE_ADMIN = 'admin';
public const ROLE_TEACHER = 'prof';
public const ROLE_STUDENT = 'eleve';

public function isAdmin(): bool
{
    return $this->hasRole(self::ROLE_ADMIN);
}

public function isTeacher(): bool
{
    return $this->hasRole(self::ROLE_TEACHER);
}
```

Ce code est utilise par les policies et les middlewares.

---

# Table pivot : classes et eleves

La relation many-to-many passe par `class_user`.

```php
public function students(): BelongsToMany
{
    return $this->belongsToMany(
        User::class,
        'class_user',
        'school_class_id',
        'user_id'
    )->withTimestamps();
}
```

Contrainte importante :

```php
$table->unique(['school_class_id', 'user_id']);
```

---

# 6. Developpement

## Modeles developpes

| Modele | Role |
|---|---|
| `User` | Utilisateur et role |
| `SchoolClass` | Classe scolaire |
| `Course` | Matiere / cours |
| `Chapter` | Module ou chapitre |
| `ChapterContent` | Texte ou video |
| `UserContentProgress` | Suivi eleve |
| `FileMetadata` | Fichiers uploades |

---

# Exemple : suivi de progression

```php
class UserContentProgress extends Model
{
    protected $fillable = [
        'user_id',
        'chapter_content_id',
        'progress_seconds',
        'is_completed',
        'last_watched_at',
    ];

    protected $casts = [
        'progress_seconds' => 'integer',
        'is_completed' => 'boolean',
        'last_watched_at' => 'datetime',
    ];
}
```

---

# Exemple : processor API

Le processor synchronise les eleves d'une classe :

```php
if ($studentsProvided && $persisted instanceof SchoolClass) {
    $newStudentIds = $this->normalizeStudentIds($studentsInput);
    $persisted->students()->sync($newStudentIds);
    $persisted->load('students');

    $this->dispatchEnrollmentEvents(
        $persisted,
        $existingStudentIds,
        $newStudentIds
    );
}
```

Ce code evite de stocker `students` comme une colonne SQL.

---

# Code : validation API

Exemple de validation pour un contenu de chapitre :

```php
new Post(rules: [
    'chapter_id' => 'required|integer|exists:chapters,id',
    'title' => 'nullable|string|max:255',
    'content' => 'required|string',
    'content_type' => 'sometimes|string|in:text,video',
    'video_url' => 'nullable|required_if:content_type,video|url',
    'duration_seconds' => 'nullable|required_if:content_type,video|integer|min:1',
    'position' => 'sometimes|integer|min:1',
])
```

La validation empeche les contenus incomplets ou incoherents.

---

# Gestion des fichiers

Fonctionnalites :

- validation des types,
- taille maximale 100 MB,
- niveau d'acces : prive, classe, public,
- telechargement securise,
- compteur de telechargement,
- association a un cours, chapitre ou contenu.

```php
public function recordDownload(): void
{
    $this->increment('download_count');
    $this->update(['last_downloaded_at' => now()]);
}
```

---

# Maquette : gestion des fichiers

```text
+-------------------------------------------------------------+
| Fichiers pedagogiques                         [+ Upload]     |
+----------------------+--------------+-----------+-----------+
| Nom                  | Categorie    | Acces     | Actions   |
+----------------------+--------------+-----------+-----------+
| cours-fractions.pdf  | PDF          | Classe    | Voir      |
| intro-video.mp4      | Video        | Classe    | Download  |
| planning.png         | Image        | Public    | Voir      |
+----------------------+--------------+-----------+-----------+
```

Chaque fichier possede des metadonnees, un niveau d'acces et un compteur de telechargements.

---

# Architecture evenementielle

Quand une action importante arrive :

```text
Action utilisateur
  -> Observer / Processor
    -> Event
      -> Listeners
        -> Logs / Stats / Notifications
```

Evenements principaux :

- `ContentProgressUpdated`
- `UserEnrolledInClass`
- `CourseCreated`
- `CourseCompleted`

---

# Schema : events et listeners

```text
Progression modifiee
        |
        v
UserContentProgressObserver
        |
        v
ContentProgressUpdated
        |
        +--------------------+
        |                    |
        v                    v
LogProgressListener   UpdateStatisticsListener
        |                    |
        v                    v
  logs Laravel          cache / statistiques
```

Avantage : on peut ajouter une notification sans modifier la logique principale.

---

# Exemple : dispatch d'evenement

```php
foreach ($newlyEnrolledUsers as $user) {
    UserEnrolledInClass::dispatch($user, $class);
}
```

Interet :

- separer la logique metier,
- ajouter facilement des notifications,
- tracer les actions,
- calculer des statistiques.

---

# 7. Securite

## Controle par roles

Roles utilises :

```php
public const ROLE_ADMIN = 'admin';
public const ROLE_TEACHER = 'prof';
public const ROLE_STUDENT = 'eleve';
```

Chaque role donne acces a un espace different.

---

# Exemple : verification de role

```php
public function isTeacher(): bool
{
    return $this->hasRole(self::ROLE_TEACHER);
}

public function hasRole(string $role): bool
{
    return self::normalizeRole($this->role)
        === self::normalizeRole($role);
}
```

Le code accepte aussi les variantes `teacher` et `student`.

---

# Exemple : policy

Principe d'une policy :

```php
public function create(User $user): bool
{
    return $user->isAdmin() || $user->isTeacher();
}
```

Elle empeche par exemple un eleve de creer un cours ou d'uploader un fichier.

---

# Code : controle d'acces fichier

Principe applique dans la policy :

```php
if ($file->access_level === 'public') {
    return true;
}

if ($file->uploaded_by === $user->id) {
    return true;
}

if ($file->access_level === 'class') {
    return $user->classes()
        ->where('school_classes.id', $file->accessible_to_class_id)
        ->exists();
}
```

Le droit depend du role, du proprietaire et de l'appartenance a une classe.

---

# Securite des fichiers

Regles d'acces :

| Niveau | Acces |
|---|---|
| private | proprietaire ou admin |
| class | utilisateurs de la classe |
| public | utilisateurs autorises |

Avant un telechargement, l'application verifie l'autorisation.

---

# 8. Tests

## Strategie de test

Le projet contient :

- tests unitaires,
- tests fonctionnels,
- tests d'integration,
- tests API,
- tests de droits,
- tests des events.

Objectif : verifier a la fois la logique metier et la securite.

---

# Exemple : test many-to-many

```php
public function test_school_class_students_many_to_many(): void
{
    $teacher = User::factory()->create(['role' => 'prof']);
    $class = SchoolClass::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $students = User::factory()->count(3)
        ->create(['role' => 'eleve']);

    foreach ($students as $student) {
        $class->students()->attach($student->id);
    }

$this->assertCount(3, $class->students);
}
```

---

# Code : test du processor

Le test verifie que `sync()` remplace les eleves au lieu de les ajouter en double :

```php
$class->students()->sync([$student1->id, $student2->id]);
$this->assertCount(2, $class->students);

$class->students()->sync([$student2->id, $student3->id]);
$class->load('students');

$this->assertFalse($class->students->contains($student1));
$this->assertTrue($class->students->contains($student2));
$this->assertTrue($class->students->contains($student3));
```

Ce test securise une regle importante de gestion de classe.

---

# Exemple : test de securite

```php
$student = User::factory()->create(['role' => 'eleve']);

$response = $this->actingAs($student)
    ->post('/api/courses', [
        'title' => 'Cours non autorise',
    ]);

$response->assertForbidden();
```

Ce type de test confirme que les permissions sont appliquees.

---

# Parcours complet teste

Scenario :

```text
Creer professeur
Creer classe
Inscrire eleve
Creer cours
Ajouter chapitres
Ajouter contenus
Mettre a jour progression
Verifier droits admin/prof/eleve
```

Ce test valide le projet comme une application complete, pas seulement comme des modules separes.

---

# Commandes de test

```bash
php artisan test
```

Tests plus cibles :

```bash
php artisan test tests/Unit/SchoolClassProcessorTest.php
php artisan test tests/Feature/FileUploadTest.php
php artisan test tests/Feature/CompleteUserJourneyTest.php
```

---

# 9. Deploiement

## Preparation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Puis configurer :

- base de donnees,
- stockage fichiers,
- environnement,
- cache,
- URL de l'application.

---

# Mise en production

```bash
php artisan migrate --force
php artisan db:seed
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Le build Vite est indispensable pour que Tailwind et le JavaScript soient servis correctement.

---

# Point d'attention front

Probleme rencontre :

```text
Page affichee sans style
Icones enormes
Formulaires non alignes
```

Cause :

```text
Assets Vite/Tailwind non recompiles
```

Solution :

```bash
npm run build
```

---

# Avant / apres compilation front

```text
Avant npm run build
  - CSS absent ou ancien
  - icones SVG trop grandes
  - formulaires non alignes
  - mise en page cassee

Apres npm run build
  - Tailwind regenere
  - composants Sirae appliques
  - layout lisible
  - interface utilisable
```

C'est un exemple concret de probleme DevOps entre code source et livrable final.

---

# Points DevOps du projet

- migrations versionnees,
- seeders de demonstration,
- tests automatises,
- documentation technique,
- separation configuration/code,
- build front reproductible,
- logs et events,
- architecture evolutive.

---

# 10. Bilan

## Points forts

- projet complet et coherent,
- separation claire des roles,
- API REST operationnelle,
- base de donnees structuree,
- gestion de fichiers,
- events et listeners,
- tests significatifs,
- documentation produite.

---

# Difficultes rencontrees

- synchronisation des eleves dans une classe,
- gestion fine des permissions,
- coherence entre API et vues Blade,
- relation many-to-many,
- suivi de progression,
- build Vite/Tailwind,
- tests d'integration.

Chaque difficulte a amene une amelioration concrete du projet.

---

# Ameliorations possibles

- statistiques avancees pour les professeurs,
- notifications email reelles,
- export des progressions,
- lecteur video plus complet,
- pipeline CI/CD,
- queue pour les listeners,
- interface admin plus riche.

---

# Conclusion

Seira montre une application Laravel complete :

- fonctionnelle,
- securisee,
- testee,
- structuree,
- documentee,
- extensible.

Le projet repond a un besoin concret : organiser et suivre l'apprentissage dans un cadre scolaire.

---

# Questions

Merci pour votre attention.
