# Documentation Technique et API

## 1. Objectif du projet

Ce projet expose une API REST pour la gestion d'un environnement scolaire:
- utilisateurs
- classes
- cours
- chapitres
- contenus de chapitre (texte/video)
- progression de visionnage
- livres

L'API est construite avec Laravel 12 + API Platform.

## 2. Stack technique

- PHP 8.2+
- Laravel 12
- API Platform Laravel 4.2
- Base de donnees relationnelle (via Eloquent)
- PHPUnit pour les tests

## 3. Points d'entree

- Prefixe API: `/api`
- Documentation Swagger UI: `/api/docs`
- OpenAPI JSON: `/api/docs.jsonopenapi`
- Page projet: `/project`

## 4. Lancement local

Depuis le dossier `devops`:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

API disponible par defaut sur:
- `http://127.0.0.1:8000/api`

## 5. Ressources exposees

Toutes les ressources ci-dessous exposent les operations standards:
- GET collection: `/api/<resource>`
- POST creation: `/api/<resource>`
- GET item: `/api/<resource>/{id}`
- PATCH item: `/api/<resource>/{id}`
- DELETE item: `/api/<resource>/{id}`

Ressources:
- `/api/users`
- `/api/school_classes`
- `/api/courses`
- `/api/chapters`
- `/api/chapter_contents`
- `/api/user_content_progresses`
- `/api/books`

## 6. Modele de securite (RBAC)

Roles metier:
- `admin`
- `prof` (alias accepte: `teacher`)
- `eleve` (alias accepte: `student`)

Regle globale:
- un admin passe tous les controles d'autorisation (Gate::before)

### 6.1 Regles principales par ressource

Users:
- liste: admin, prof
- detail: admin, prof, utilisateur lui-meme
- creation: admin
- modification: admin ou utilisateur lui-meme
- suppression: admin (sauf auto-suppression)

SchoolClass:
- liste: admin, prof
- detail: admin, prof proprietaire de la classe, ou eleve inscrit
- creation/modification/suppression: admin ou prof proprietaire

Course:
- liste: admin, prof
- detail: admin, prof proprietaire du cours, ou eleve inscrit dans la classe du cours
- creation/modification/suppression: admin ou prof proprietaire

Chapter et ChapterContent:
- liste: admin, prof
- detail: admin, prof proprietaire du cours parent, ou eleve inscrit dans la classe du cours parent
- creation/modification/suppression: admin ou prof proprietaire

UserContentProgress:
- liste: admin uniquement
- detail/modification/suppression:
  - admin
  - eleve sur sa propre progression
  - prof du cours rattache
- creation:
  - admin
  - eleve pour lui-meme
  - prof du cours rattache

Book:
- policies dediees actives (admin bypass applique)

## 7. Validations importantes

Exemples de validations metier:

- `users.email` unique
- `role` dans: `admin, prof, eleve, teacher, student`
- relations acceptees en ID ou en IRI API (ex: `/api/users/1`)
- `chapter_contents.content_type` dans `text|video`
- si `content_type=video` alors:
  - `video_url` requis et URL valide
  - `duration_seconds` requis et entier >= 1
- `user_content_progress.progress_seconds` entier >= 0
- `user_content_progress.is_completed` booleen

## 8. Format des relations

Le projet accepte deux formats pour certaines relations:
- format numerique: `teacher_id: 1`
- format IRI API Platform: `teacher: "/api/users/1"`

Exemple creation de cours:

```json
{
  "title": "Histoire - 5A",
  "description": "Cours d'histoire",
  "schoolClass": "/api/school_classes/1",
  "teacher": "/api/users/1"
}
```

Exemple creation de progression video:

```json
{
  "user": "/api/users/12",
  "chapterContent": "/api/chapter_contents/42",
  "progress_seconds": 280,
  "is_completed": false,
  "last_watched_at": "2026-03-25 10:30:00"
}
```

## 9. Exemples de requetes

### 9.1 Recuperer la liste des classes

```bash
curl -X GET http://127.0.0.1:8000/api/school_classes
```

### 9.2 Creer un chapitre

```bash
curl -X POST http://127.0.0.1:8000/api/chapters \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Chapitre 1",
    "position": 1,
    "course": "/api/courses/1"
  }'
```

### 9.3 Mettre a jour une progression

```bash
curl -X PATCH http://127.0.0.1:8000/api/user_content_progresses/1 \
  -H "Content-Type: application/merge-patch+json" \
  -d '{
    "progress_seconds": 600,
    "is_completed": true
  }'
```

## 10. Commandes de verification

```bash
php artisan migrate:fresh --seed
php artisan test
```

## 11. Durcissement securite (configurable)

Le projet inclut des options de securite API activables via `.env`:

- `API_REQUIRE_AUTH=false` (defaut):
  - `false`: API accessible sans middleware `auth` global
  - `true`: middleware `auth` applique globalement a toutes les routes API Platform
- `API_SWAGGER_UI_ENABLED=true` hors production par defaut:
  - en production, la valeur par defaut est `false`
  - definir explicitement `true` si vous souhaitez exposer Swagger UI

Exemple `.env` pour un environnement plus strict:

```dotenv
API_REQUIRE_AUTH=true
API_SWAGGER_UI_ENABLED=false
```
