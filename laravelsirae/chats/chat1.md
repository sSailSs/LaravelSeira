# Plateforme e-learning scolaire

_Started 2026-06-01 12:28 UTC_

---

## User

Salut claude, j'ai besoin de 3 templates qui correspond à école, formateur et expérience élève

expérience élève, tu peux ajouter un template en plus pour la lecture de vidéo (la lecture se vidéo sera du navigateur natif, pas besoin de design)

voici des éléments pour que tu es du contexte

# Profils principaux

Trois profils principaux :

- Administrateur école
- Formateur
- Élève

Un utilisateur peut gérer une ou plusieurs écoles.

---

## École

Une école peut :

- Créer des matières
- Créer des classes
- Associer des formateurs
- Assigner des élèves à des classes

---

## Matière

Une matière contient :

- Nom
- Référentiel (PDF téléversé)
- Attentes pédagogiques
- Volume horaire attendu
- Classes associées
- Liste de contenus vidéo

---

## Formateur

Un formateur peut :

- Ajouter des vidéos à une matière
- Organiser les vidéos en séquence pédagogique

---

## 🎥 Expérience élève

Lorsqu'un élève se connecte :

1. Il voit les classes qui lui sont attribuées.
2. Il accède aux matières correspondantes.
3. Il ouvre un cours.
4. Il voit la liste des vidéos à visionner.
5. Il bénéficie d'un suivi en temps réel de sa progression.

## Assistant

_[tool: read_file]_

Salut ! Je vais lire le brief de design d'abord pour bien comprendre le contexte.

_[tool: questions_v2]_

J'ai lu le brief en détail. Avant de me lancer, je veux cadrer la direction visuelle — il n'y a pas encore de design system ni de charte attachée, et c'est essentiel pour éviter de partir dans une direction qui ne vous correspond pas.

Quelques précisions sur le contexte avant les questions :
- Le brief parle de **Seira** avec les rôles Admin/Professeur/Élève et une hiérarchie Classe → Cours → Chapitre → Contenu.
- Votre message parle d'**école/formateur/élève** avec Matière → séquence pédagogique.
- Le projet s'appelle **LaravelSirae**.

Je vais clarifier ce vocabulaire ci-dessous, en plus de la direction visuelle.

## User

Questions answered:
- terminology: École / Formateur / Élève + Matière (votre message)
- audience_age: Étudiants / formation pro (adultes)
- vibe: Sobre et institutionnel (sérieux, fiable)
- color: 2
- type: Sans-serif neutre et lisible (Inter-like)
- scope: École = dashboard global + gestion (matières/classes/formateurs)
- variations: Non, une seule direction propre par template
- navigation: Sidebar latérale fixe (classique app SaaS)
- tweaks: Mode clair / sombre
- logo: Non, mets un placeholder propre
- format: Maquettes pensées pour être transposées en Blade/Tailwind ensuite

## Assistant

_[tool: update_todos]_

Parfait, j'ai tout ce qu'il me faut. Voici la direction que je retiens :

- **Vocabulaire** : École / Formateur / Élève + Matière
- **Public** : adultes (formation pro) → ton **sobre & institutionnel**
- **Palette** : neutres chauds (crème/beige) + accent **terracotta** (option 2), utilisé avec retenue
- **Type** : Public Sans (sans-serif neutre, lisible, vocation institutionnelle — alternative propre à Inter)
- **Layout** : sidebar latérale fixe, **mode clair/sombre** intégré
- **Format** : structure pensée pour transposition **Blade + Tailwind** → j'utilise Tailwind (config + tokens en variables CSS) pour que ce soit repris quasi tel quel
- **Templates** : École (dashboard + gestion), Formateur (matière + séquence vidéo), Élève (mes matières + progression), + Lecteur vidéo (player natif)

Je monte le tout.

_[tool: write_file]_

_[tool: write_file]_

_[tool: str_replace_edit]_

_[tool: write_file]_

Maintenant les templates. Je commence par l'École (dashboard + gestion), qui sert de référence pour la coquille (sidebar + topbar + thème).

_[tool: show_html]_

_[tool: show_html]_

_[tool: save_screenshot]_

_[tool: view_image]_

_[tool: write_file]_

École impeccable — sobre, chaleureux, lisible. Je passe au **Formateur** (gestion d'une matière + organisation des vidéos en séquence, avec réorganisation par glisser-déposer).

_[tool: show_html]_

_[tool: save_screenshot]_

_[tool: view_image]_

_[tool: write_file]_

Le **Formateur** est nickel (drag-réorganisation, modal d'upload, panneau référentiel/attentes/volume). Maintenant l'**Élève** (mes matières + progression).

_[tool: write_file]_

Et enfin le **Lecteur vidéo** — player natif + suivi de progression en temps réel + playlist par module, avec reprise automatique persistée.

_[tool: write_file]_

Maintenant la page d'accueil (hub de navigation entre les 4 templates + résumé du système).

_[tool: update_todos]_

Je vérifie le lecteur (l'écran le plus interactif) et le dashboard élève avant de livrer.

_[tool: show_html]_

_[tool: get_webview_logs]_

_[tool: save_screenshot]_

_[tool: view_image]_

_[tool: str_replace_edit]_

Le lecteur fonctionne (vidéo native chargée, reprise à 48 %, suivi temps réel, playlist avec coches). Un seul détail à corriger : le compteur du Module 2 affiche « 0/2 » alors qu'une vidéo y est terminée.

_[tool: show_html]_

_[tool: save_screenshot]_

_[tool: view_image]_

_[tool: done]_

Dark mode superbe et cohérent. Tout est en place. Je livre via la page d'accueil et lance la vérification.

_[tool: fork_verifier_agent]_

Voilà les **4 templates** prêts, reliés entre eux par une page d'accueil :

| Template | Contenu |
|---|---|
| **École — Vue d'ensemble** | Dashboard admin : KPIs, table des matières (formateur/classes/volume/progression), classes, activité, switcher d'établissement |
| **Formateur — Matière** | Séquence pédagogique **réordonnable au glisser-déposer**, modal d'upload, panneau référentiel PDF / attentes / volume horaire |
| **Élève — Mes cours** | Carte « Reprendre », anneau de progression globale, grille des matières avec barres de progression |
| **Élève — Lecteur vidéo** | **Player natif du navigateur**, suivi de progression **en temps réel**, reprise automatique persistée, playlist par module avec coches |

**Direction retenue** : sobre/institutionnel, accent terracotta sur neutres chauds, Public Sans, **mode clair/sombre** intégré (bouton soleil/lune dans chaque topbar). Les tokens sont en variables CSS mappées dans la config Tailwind (`bg-canvas`, `text-ink`, `bg-accent`…) → **transposable quasi tel quel en Blade + Tailwind**, avec composants réutilisables dans `assets/sirae.css` (`.card`, `.btn`, `.nav-item`, `.track`, `.badge`…).

Quelques pistes pour la suite si tu veux pousser :
- Écrans secondaires (création de cours côté formateur en plein écran, gestion des classes côté école, login/register).
- Convertir le panneau de droite du lecteur en off-canvas sur mobile (actuellement masqué < lg).
- Brancher la vidéo de démo sur tes vrais fichiers (le clip actuel ne fait que 10 s, juste pour tester la mécanique de progression).

Dis-moi si tu veux ajuster le vocabulaire, ajouter des écrans, ou affiner un détail visuel.

## User

comment je fais pour brancher ça à claude code ?

## Assistant

_[tool: invoke_skill]_

Bonne question — il y a un flux dédié pour ça. Je récupère la procédure.

_[tool: write_file]_

