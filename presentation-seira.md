---
marp: true
theme: default
paginate: true
size: 16:9
style: |
  :root {
    --ink: #17211c;
    --muted: #5b665f;
    --line: #d7dfd9;
    --paper: #fbfaf6;
    --soft: #edf4ee;
    --mint: #9fc7ad;
    --leaf: #2f6f52;
    --clay: #b7633f;
    --gold: #d8a846;
  }
  section {
    background: var(--paper);
    color: var(--ink);
    font-family: Arial, Helvetica, sans-serif;
    letter-spacing: 0;
    padding: 44px 58px;
  }
  section::after {
    color: var(--muted);
    font-size: 18px;
  }
  h1 {
    color: var(--leaf);
    font-size: 42px;
    margin-bottom: 18px;
  }
  h2 {
    color: var(--ink);
    font-size: 30px;
    margin: 0 0 16px;
  }
  h3 {
    color: var(--clay);
    font-size: 22px;
    margin: 14px 0 10px;
  }
  p, li {
    font-size: 24px;
    line-height: 1.32;
  }
  small {
    color: var(--muted);
  }
  strong {
    color: var(--leaf);
  }
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 20px;
  }
  th {
    background: var(--leaf);
    color: white;
  }
  th, td {
    border: 1px solid var(--line);
    padding: 9px 12px;
  }
  code {
    font-size: 0.9em;
  }
  pre {
    border-radius: 8px;
    background: #17211c;
    padding: 16px 18px;
    overflow: hidden;
  }
  pre code {
    color: #f7fff8;
    font-size: 19px;
    line-height: 1.28;
    white-space: pre-wrap;
    word-break: break-word;
  }
  .cover {
    background:
      linear-gradient(90deg, rgba(251,250,246,.94), rgba(251,250,246,.72)),
      url("wireframe/school%20preview.png");
    background-size: cover;
    background-position: center;
  }
  .cover h1 {
    font-size: 64px;
    color: var(--leaf);
    margin-top: 170px;
  }
  .cover p {
    max-width: 760px;
    color: var(--ink);
  }
  .tag {
    display: inline-block;
    color: white;
    background: var(--clay);
    border-radius: 999px;
    padding: 7px 14px;
    font-size: 18px;
    font-weight: bold;
  }
  .grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }
  .grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }
  .card {
    background: white;
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 18px;
    box-shadow: 0 10px 28px rgba(23,33,28,.08);
  }
  .card h3 {
    margin-top: 0;
  }
  .metric {
    font-size: 42px;
    color: var(--clay);
    font-weight: bold;
    margin: 0;
  }
  .note {
    background: var(--soft);
    border-left: 7px solid var(--mint);
    padding: 14px 18px;
    border-radius: 8px;
  }
  .pdf-frame {
    width: 100%;
    height: 470px;
    border: 1px solid var(--line);
    border-radius: 8px;
    background: white;
  }
  .wire {
    border: 1px solid var(--line);
    border-radius: 8px;
    max-height: 460px;
    object-fit: contain;
    background: white;
  }
  .schema-img {
    display: block;
    max-width: 100%;
    max-height: 485px;
    object-fit: contain;
    margin: 0 auto;
    border: 1px solid var(--line);
    border-radius: 8px;
    background: white;
  }
  .diagram {
    display: grid;
    gap: 14px;
  }
  .diagram-row {
    display: grid;
    grid-template-columns: 210px 1fr;
    gap: 14px;
    align-items: stretch;
  }
  .actor {
    background: var(--leaf);
    color: white;
    border-radius: 8px;
    padding: 14px;
    font-weight: bold;
    text-align: center;
  }
  .uses {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }
  .usecase {
    background: white;
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: 9px 14px;
    font-size: 18px;
    box-shadow: 0 8px 18px rgba(23,33,28,.06);
  }
  .flow {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    align-items: center;
  }
  .step {
    background: white;
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 13px;
    min-height: 92px;
    font-size: 18px;
  }
  .arrow {
    color: var(--clay);
    font-size: 28px;
    font-weight: bold;
    text-align: center;
  }
  .db {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
  }
  .tablebox {
    background: white;
    border: 1px solid var(--line);
    border-top: 7px solid var(--leaf);
    border-radius: 8px;
    padding: 12px;
    min-height: 92px;
    font-size: 17px;
  }
  .tablebox strong {
    display: block;
    margin-bottom: 6px;
  }
  .compact li {
    font-size: 21px;
    margin-bottom: 6px;
  }
  .small-code pre, pre.small-code {
    font-size: 17px;
  }
---

<!-- _class: cover -->

# Monto

Plateforme e-learning scolaire pour organiser les classes, publier des cours et suivre la progression des eleves.

<span class="tag">Laravel / API Platform / Blade / DevOps</span>

---

# Sommaire

<div class="grid-2">
<div class="card compact">

1. Presentation du projet
2. Cadrage / organisation
3. Conception fonctionnelle
4. Architecture technique
5. Base de donnees

</div>
<div class="card compact">

6. Developpement
7. Securite
8. Tests
9. Deploiement
10. Bilan

</div>
</div>

---

# 1. Presentation du projet

## Le besoin

Centraliser un environnement scolaire simple et coherent :

<div class="grid">
<div class="card">
<h3>Admin</h3>
<p>Supervise les utilisateurs, les classes, les cours et les donnees.</p>
</div>
<div class="card">
<h3>Professeur</h3>
<p>Cree des cours, structure les chapitres et publie les contenus.</p>
</div>
<div class="card">
<h3>Eleve</h3>
<p>Consulte les cours de sa classe et reprend sa progression.</p>
</div>
</div>

---

# Objectifs de demonstration

| Attendu | Reponse dans Monto |
|---|---|
| Application fonctionnelle | Espaces admin, prof et eleve |
| Conception | Cas d'utilisation, sequence, classes, MPD |
| Architecture | Laravel, API Platform, policies, events |
| Qualite | Tests API, tests RBAC, parcours complet |
| DevOps | Migrations, seeders, build Vite, cache prod |

<div class="note">Fil conducteur : creer un cours, publier un contenu, l'ouvrir cote eleve, puis enregistrer la progression.</div>

---

# 2. Cadrage / organisation

## Perimetre realise

<div class="grid-2 compact">
<div class="card">

- Authentification par session
- Redirection vers l'espace du role
- Gestion classes, cours, chapitres
- Contenus texte et video
- Suivi de progression

</div>
<div class="card">

- API REST documentee
- Upload et download de fichiers
- Policies RBAC
- Events et listeners
- Tests automatises

</div>
</div>

---

# Organisation du code

```text
app/
  Models/        Entites Eloquent exposees en API
  Policies/      Regles d'autorisation
  Events/        Evenements metier
  Listeners/     Reactions aux evenements
  State/         Processors API Platform
  Http/
    Controllers/ Vues web Blade

database/
  migrations/    Structure SQL versionnee
  factories/     Donnees de test
  seeders/       Donnees de demonstration
```

---

# Methode de travail

| Etape | Livrable |
|---|---|
| Analyse | brief fonctionnel, roles, parcours |
| Conception | cas d'utilisation, classe, sequence, MPD |
| Implementation | modeles, routes, API, vues, policies |
| Verification | tests unitaires, API, parcours complet |
| Livraison | build front, migrations, documentation |

---

# Choix et limites

<div class="grid-2">
<div class="card">
<h3>Choix assumes</h3>

- Blade + Tailwind pour rester proche de Laravel
- API Platform pour exposer rapidement les ressources
- Policies Laravel pour securiser chaque ressource
- Events/listeners pour separer les effets metier

</div>
<div class="card">
<h3>Points limites</h3>

- Pas encore de vraie CI/CD
- Notifications preparees mais pas industrialisees
- Front fonctionnel, ameliorable en design system complet
- Swagger a durcir en production

</div>
</div>

---

# 3. Conception fonctionnelle

## Cas d'utilisation

<img class="schema-img" src="presentation-assets/cas-utilisation.png" />

<small>Source : `Cas Utilisation.pdf` / rendu depuis `diagramme-cas-utilisation-seira.puml`.</small>

---

# Parcours principal

## Scenario eleve

<img class="schema-img" src="presentation-assets/sequence.png" />

<small>Source : `Sequence.pdf` / rendu depuis `diagramme-sequence-seira.puml`.</small>

---

# Wireframes

<div class="grid-2">
<div>
<h3>Espace eleve</h3>
<img class="wire" src="wireframe/eleve%20mes%20cours.png" />
</div>
<div>
<h3>Lecture video</h3>
<img class="wire" src="wireframe/eleve%20lecture%20video.png" />
</div>
</div>

---

# 4. Architecture technique

## Stack

| Couche | Technologie |
|---|---|
| Backend | Laravel 12 / PHP |
| API | API Platform Laravel |
| ORM | Eloquent |
| Front | Blade, TailwindCSS, Alpine.js |
| Build | Vite |
| Tests | PHPUnit |
| Base | SQL via migrations Laravel |

---

# Architecture applicative

```text
Navigateur
   |
   v
Routes web / routes API
   |
   +--> Controllers Blade --> vues par role
   |
   +--> API Platform ------> ressources REST
             |
             v
      Policies + Models Eloquent
             |
             v
        Base de donnees
```

<div class="note">Les droits ne sont pas portes par l'interface : ils sont verifies dans Laravel.</div>

---

# Routes et API

```php
Route::middleware('auth')->group(function () {
    Route::get('/space/admin', [LearningSpaceController::class, 'admin'])
        ->middleware('role:admin');

    Route::get('/space/prof', [LearningSpaceController::class, 'prof'])
        ->middleware('role:prof');

    Route::get('/space/eleve', [LearningSpaceController::class, 'eleve'])
        ->middleware('role:eleve');
});
```

API documentee :

```text
/api/users, /api/school_classes, /api/courses, /api/chapters,
/api/chapter_contents, /api/user_content_progresses, /api/file_metadata
```

---

# Events et listeners

```text
Action utilisateur
  -> Controller / Processor / Observer
    -> Event metier
      -> Listener
        -> logs, statistiques, notifications futures
```

| Event | Role |
|---|---|
| `UserEnrolledInClass` | tracer une inscription |
| `ContentProgressUpdated` | suivre la progression |
| `CourseCreated` | reagir a une creation de cours |
| `CourseCompleted` | preparer stats et notifications |

---

# 5. Base de donnees

## MPD

<img class="schema-img" src="presentation-assets/mpd.png" />

<small>Source : `MPD.pdf` / rendu depuis `mpd-sirae.puml`.</small>

---

# Schema de classes

<img class="schema-img" src="presentation-assets/classes.png" />

<small>Source complete : `diagramme-classes-sirae.mmd`.</small>

---

# Contraintes importantes

| Table | Point cle |
|---|---|
| `class_user` | relation many-to-many eleves/classes |
| `courses` | rattache un cours a une classe et un professeur |
| `chapters` | ordre par `position` dans un cours |
| `chapter_contents` | contenu texte ou video |
| `user_content_progress` | unique par utilisateur + contenu |
| `file_metadata` | fichier polymorphe + niveau d'acces |

---

# 6. Developpement

## Modeles principaux

<div class="grid">
<div class="card">
<p class="metric">3</p>
<p>roles : admin, prof, eleve</p>
</div>
<div class="card">
<p class="metric">5</p>
<p>niveaux pedagogiques : classe, cours, chapitre, contenu, progression</p>
</div>
<div class="card">
<p class="metric">REST</p>
<p>ressources exposees via API Platform</p>
</div>
</div>

---

# Exemple de code : modeles

```php
class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'school_class_id',
        'teacher_id',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }
}
```

---

# API : validation et synchronisation

```php
new Post(rules: [
    'title' => 'required|string|max:255',
    'school_class_id' => 'required_without:schoolClass|integer',
    'schoolClass' => ['required_without:school_class_id',
        'regex:/^(\/api\/school_classes\/\d+|\d+)$/'],
])
```

```php
$persisted->students()->sync($newStudentIds);
```

<div class="note">Le processor synchronise les eleves dans la table pivot au lieu de stocker une liste dans une colonne.</div>

---

# Fichiers et interface prof

<div class="grid-2">
<div class="compact">

- upload PDF, video, image, docx
- taille maximale : 100 MB
- acces : `private`, `class`, `public`
- compteur de telechargements
- association a un cours, chapitre ou contenu

</div>
<div>
<img class="wire" src="wireframe/prof%20matiere.png" />
</div>
</div>

---

# Wireframes cote professeur

<div class="grid-2">
<div>
<h3>Tableau de bord</h3>
<img class="wire" src="wireframe/prof%20tableau%20de%20bord.png" />
</div>
<div>
<h3>Vue matiere</h3>
<img class="wire" src="wireframe/prof%20matiere.png" />
</div>
</div>

---

# 7. Securite

## RBAC

```php
public const ROLE_ADMIN = 'admin';
public const ROLE_TEACHER = 'prof';
public const ROLE_STUDENT = 'eleve';

public function hasRole(string $role): bool
{
    return self::normalizeRole($this->role)
        === self::normalizeRole($role);
}
```

| Role | Acces |
|---|---|
| Admin | supervision globale |
| Prof | ses classes, ses cours, ses contenus |
| Eleve | ses cours et sa progression |

---

# Policies

```php
public function create(User $user): bool
{
    return $user->isAdmin() || $user->isTeacher();
}
```

<div class="grid-2 compact">
<div class="card">
<h3>Ce que ca protege</h3>

- creation de cours
- modification de classes
- consultation de progression
- acces aux fichiers

</div>
<div class="card">
<h3>Ce que les tests verifient</h3>

- 403 pour les actions interdites
- filtrage selon la classe
- professeur limite a ses ressources
- admin avec bypass global

</div>
</div>

---

# Securite fichiers et production

| Sujet | Mesure |
|---|---|
| Fichiers prives | proprietaire ou admin |
| Fichiers de classe | inscription dans la classe |
| Fichiers publics | acces autorise |
| API | policies sur les ressources |
| Production | `API_REQUIRE_AUTH=true` possible |
| Swagger | desactivation possible hors besoin |

---

# 8. Tests

## Strategie

| Type | Objectif |
|---|---|
| Unitaires | relations Eloquent et logique metier |
| Feature | routes, vues, uploads |
| API | validation, CRUD, formats ID/IRI |
| Securite | RBAC et 403 |
| Integration | parcours utilisateur complet |

Commandes :

```bash
php artisan test
php artisan test tests/Feature/CompleteUserJourneyTest.php
```

---

# Parcours complet teste

```text
Admin cree professeur et classe
Professeur cree cours, chapitres et contenus
Admin inscrit Marie Dubois en 6A
Eleve consulte le cours
Eleve regarde 600s / 1200s
Progression mise a jour
Evenement ContentProgressUpdated emis
Prof et admin consultent les progressions
Autres eleves recoivent 403
```

<div class="note">Ce test prouve que les modules fonctionnent ensemble, pas seulement un par un.</div>

---

# 9. Deploiement

## Lancement local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
npm run dev
```

Points a configurer :

- base de donnees
- stockage fichiers
- URL applicative
- mode API et Swagger

---

# Mise en production

```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

<div class="note">Le build Vite est essentiel : sans lui, Tailwind et les composants front peuvent apparaitre casses.</div>

---

# Points DevOps retenus

<div class="grid-2 compact">
<div class="card">
<h3>Fiabilite</h3>

- migrations versionnees
- seeders de demonstration
- tests automatises
- logs via events/listeners

</div>
<div class="card">
<h3>Livraison</h3>

- configuration separee dans `.env`
- build front reproductible
- caches Laravel
- documentation API

</div>
</div>

---

# 10. Bilan

<div class="grid">
<div class="card">
<h3>Fonctionnel</h3>
<p>Parcours clair pour admin, prof et eleve.</p>
</div>
<div class="card">
<h3>Technique</h3>
<p>Laravel structure, API REST, ORM, policies et events.</p>
</div>
<div class="card">
<h3>Qualite</h3>
<p>Tests coherents, documentation et logique de deploiement.</p>
</div>
</div>

Ameliorations possibles : statistiques avancees, notifications email, export des progressions, pipeline CI/CD, design system complet.

## Questions

Merci pour votre attention.
