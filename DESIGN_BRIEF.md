# Seira — Plateforme e-learning scolaire (Brief Design)

## Concept global

**Seira** est une plateforme de gestion pédagogique pour établissements scolaires. Elle s'adresse à trois profils d'utilisateurs avec des espaces et permissions distincts :

| Rôle | Verbe clé | Ce qu'il fait |
|---|---|---|
| **Admin** | Supervise | Vue globale sur toutes les classes, cours, utilisateurs |
| **Professeur** | Enseigne | Crée des cours, gère ses classes, publie du contenu (vidéos + textes) |
| **Élève** | Apprend | Accède aux cours de sa classe, regarde des vidéos, suit sa progression |

---

## Modèle de données (hiérarchie pédagogique)

```
SchoolClass (ex: 6A, 5B)
  └── Course (ex: Mathématiques — 6A)
        └── Chapter (ex: Chapitre 1 — Fractions)
              └── ChapterContent (vidéo OU texte/HTML)
                    └── UserContentProgress (progression en secondes, is_completed)
```

Chaque cours appartient à **une classe** et à **un professeur**. Les élèves accèdent uniquement aux cours de leur(s) classe(s). La progression vidéo est traquée par utilisateur + contenu (secondes vues, terminé ou non).

---

## Fonctionnalités clés à designer

### Espace Élève
- **Mes cours** : liste des cours des classes auxquelles l'élève appartient
- **Lecteur de contenu** : vidéo avec reprise automatique à la dernière position (progress_seconds), passage au prochain contenu
- **Progression** : indicateurs visuels par chapitre / cours (% terminé)

### Espace Professeur
- **Mes classes** : liste des élèves, infos de classe
- **Mes cours** : créer un cours (titre, description, classe), gérer les chapitres et contenus
- **Upload de fichiers** : documents PDF, vidéos, images — avec niveaux d'accès (privé, classe, public)

### Espace Admin
- **Vue globale** : tous les cours, classes, utilisateurs
- **Statistiques** : nb élèves, cours, progressions
- Accès total sans restrictions

---

## Ce qui existe côté back-end

- API REST complète et documentée (Swagger sur `/api/docs`)
- Auth par sessions (Breeze) — pas de JWT/SPA pour l'instant
- Politiques d'accès en place (RBAC) pour tous les modèles
- Système d'événements : `CourseCreated`, `ContentProgressUpdated`, `UserEnrolledInClass`, `CourseCompleted`
- Upload/download de fichiers (PDF, MP4, images, docx — max 100MB)

---

## Stack front actuelle (à remplacer)

- **Blade** templates très basiques, peu ou pas de composants réutilisables
- **TailwindCSS** + **Alpine.js** (léger)
- **Vite** comme bundler
- Design inconsistant, peu professionnel, pas de vrai système de design

Le front actuel est fonctionnel mais **purement fonctionnel** — aucune hiérarchie visuelle claire, pas de navigation fluide entre les contenus, pas de feedback visuel sur la progression.

---

## Ce que le nouveau front doit résoudre

1. **Navigation par rôle claire** : après login, l'utilisateur arrive dans son espace avec une UI adaptée à ce qu'il fait vraiment
2. **Lecteur de cours fluide** : navigation chapitre → contenu → suivant, reprise vidéo, marquage terminé
3. **Hiérarchie visuelle** : Classe → Cours → Chapitre → Contenu, qu'on comprend d'un coup d'œil
4. **Formulaires de création (prof)** : créer un cours, ajouter des chapitres et des contenus sans friction
5. **Composants réutilisables** : card de cours, badge de progression, lecteur vidéo, liste de fichiers

---

## Contraintes techniques pour le design

- Le back-end tourne en **Laravel + Blade** (pas de SPA Vue/React en place) — le nouveau front peut rester en **Blade + Alpine.js/HTMX** ou migrer vers une **SPA Vue 3**
- Les données passent soit par les **vues Blade** (données injectées par le contrôleur), soit par l'**API REST** (`/api/*`)
- L'auth est en **session cookie** (pas Bearer token)
- TailwindCSS est déjà installé et peut être étendu avec un thème personnalisé

---

## Pages / écrans attendus

| Écran | Rôle | Priorité |
|---|---|---|
| Login / Register | Tous | Haute |
| Dashboard Élève | Élève | Haute |
| Lecteur de contenu (vidéo + texte) | Élève | Haute |
| Dashboard Professeur | Prof | Haute |
| Création / édition de cours | Prof | Haute |
| Gestion chapitres & contenus | Prof | Haute |
| Dashboard Admin | Admin | Moyenne |
| Profil utilisateur | Tous | Basse |

---

## Structure de routes web existantes

```
GET  /                    → Page d'accueil publique
GET  /dashboard           → Redirection vers l'espace du rôle connecté
GET  /space/admin         → Dashboard admin
GET  /space/prof          → Dashboard professeur
GET  /space/eleve         → Dashboard élève
GET  /profile             → Édition du profil
GET  /files/{file}/view   → Visualisation fichier inline
GET  /files/{file}/download → Téléchargement fichier
```

---

## API REST disponibles

Endpoints générés automatiquement par API Platform sur `/api/*` :

- `GET /api/users`
- `GET /api/school_classes`
- `GET /api/courses`
- `GET /api/chapters`
- `GET /api/chapter_contents`
- `GET /api/user_content_progresses`
- `GET /api/file_metadata`

Documentation interactive : `GET /api/docs` (Swagger UI)
