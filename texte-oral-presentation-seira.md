## Slide 1 - Seira

Bonjour, je vais vous presenter mon projet qui s'appelle Seira.

Seira est une plateforme e-learning scolaire. L'objectif est de permettre a un etablissement de mieux organiser ses cours, ses classes, ses contenus pedagogiques et la progression des eleves.

Le projet a ete realise avec Laravel cote backend, API Platform pour l'API REST, Blade et Tailwind pour l'interface, et une logique DevOps avec migrations, tests et build front.

Je vais essayer de presenter le projet de facon progressive : d'abord le besoin, ensuite la conception, puis la partie technique et enfin la verification avec les tests et le deploiement.

---

## Slide 2 - Presentation du projet

Le projet tourne autour de trois types d'utilisateurs.

D'abord, l'administrateur. Il supervise la plateforme, les utilisateurs, les classes et les cours.

Ensuite, le professeur. Son role est de creer les cours, d'organiser les chapitres et d'ajouter des contenus comme du texte, des videos ou des fichiers.

Enfin, l'eleve. Il consulte les cours de sa classe et sa progression est enregistree, par exemple quand il regarde une video.

Le but est donc de donner a chaque utilisateur un espace adapte. L'administrateur n'a pas les memes besoins qu'un professeur, et un eleve ne doit pas avoir acces aux memes actions qu'un professeur. Cette separation des roles est vraiment un point central du projet.

---

## Slide 3 - Objectifs de demonstration

Dans cette presentation, je vais montrer que le projet repond aux attendus principaux.

Il y a une partie fonctionnelle avec les espaces admin, professeur et eleve.

Il y a aussi une partie conception avec les cas d'utilisation, le diagramme de sequence, le diagramme de classes et le MPD.

Ensuite, je presenterai l'architecture Laravel, la base de donnees, le developpement, la securite, les tests, puis le deploiement.

Le fil conducteur est simple : un professeur cree un cours, ajoute un contenu, l'eleve consulte ce contenu, et l'application sauvegarde sa progression.

Ce fil conducteur permet aussi de montrer que les differentes parties du projet sont reliees. La base de donnees, les modeles, les routes, les policies et les tests servent tous ce meme parcours.

---

## Slide 4 - Cadrage / organisation

Sur cette slide, je presente le perimetre realise.

Le projet gere l'authentification, les roles, les classes, les cours, les chapitres et les contenus.

Il gere aussi la progression des eleves, les fichiers, une API REST documentee, les regles de securite avec les policies, les evenements et les tests.

L'idee etait de construire une application complete, pas seulement une petite page ou un CRUD isole.

Par exemple, un simple CRUD de cours ne suffit pas ici. Il faut aussi verifier qui a le droit de creer le cours, a quelle classe il appartient, quels eleves peuvent le voir, et comment suivre leur progression ensuite.

---

## Slide 5 - Organisation du code

Ici, on voit comment le projet est organise dans Laravel.

Dans `app/Models`, on trouve les entites principales comme User, Course, Chapter ou SchoolClass.

Dans `Policies`, on retrouve les regles d'autorisation. C'est ce qui permet de verifier si un utilisateur a le droit de voir, creer ou modifier une ressource.

Il y a aussi des Events et des Listeners pour separer certaines actions metier. Par exemple, quand une progression est mise a jour, on peut declencher un evenement.

Enfin, dans `database`, les migrations definissent la structure de la base, et les seeders/factories servent aux donnees de test ou de demonstration.

Cette organisation m'a aide a garder le projet lisible. Quand je cherche une regle de securite, je vais dans les policies. Quand je cherche une structure de table, je vais dans les migrations. Et quand je cherche une relation metier, je vais dans les modeles.

---

## Slide 6 - Methode de travail

Pour travailler sur le projet, j'ai suivi plusieurs etapes.

D'abord, j'ai analyse le besoin : quels sont les roles, quels sont les parcours, et quelles fonctionnalites sont importantes.

Ensuite, j'ai fait la conception avec les differents diagrammes.

Puis je suis passe a l'implementation avec les modeles, les routes, l'API, les vues et les policies.

Apres ca, j'ai verifie le comportement avec les tests.

Et pour finir, j'ai prepare la partie livraison avec les migrations, le build front et la documentation.

Cette methode m'a permis d'eviter de coder directement sans vision globale. Les schemas ont servi de support pour verifier que le code correspondait bien au besoin.

---

## Slide 7 - Choix et limites

J'ai fait certains choix techniques.

J'ai garde Blade et Tailwind parce que c'est simple a integrer dans Laravel et suffisant pour ce projet.

J'ai utilise API Platform pour exposer rapidement les ressources en REST, avec de la validation.

Pour la securite, j'ai utilise les policies Laravel, parce que c'est une maniere propre de centraliser les droits.

Il y a aussi des limites. Par exemple, il n'y a pas encore de pipeline CI/CD complet. Les notifications sont preparees mais pas encore totalement industrialisees. Et le front pourrait encore etre ameliore avec un vrai design system.

J'ai prefere etre transparent sur ces limites, parce qu'un projet peut toujours evoluer. L'important ici est que les bases soient propres : les donnees sont structurees, les droits existent, et les tests couvrent les parcours importants.

---

## Slide 8 - Cas d'utilisation

Ce schema montre les cas d'utilisation principaux.

On voit les trois acteurs : administrateur, professeur et eleve.

L'administrateur peut gerer les utilisateurs, les classes, consulter la plateforme et gerer les fichiers.

Le professeur peut gerer ses classes, ses cours, ajouter des contenus et suivre la progression des eleves.

L'eleve peut consulter ses cours, lire les contenus, regarder les videos et suivre sa progression.

Ce schema aide a verifier que chaque role a bien des actions differentes.

Par exemple, si on prend l'action "gerer les cours", elle concerne surtout le professeur. L'eleve, lui, doit seulement pouvoir consulter ses cours. Cette difference parait simple, mais elle est tres importante pour la securite et pour l'organisation de l'interface.

Ce diagramme m'a donc servi a poser les limites du projet : qui fait quoi, et surtout qui ne doit pas pouvoir faire quoi.

---

## Slide 9 - Parcours principal

Ce diagramme de sequence montre un parcours simple : un eleve regarde une video.

L'eleve se connecte, demande ses cours, puis l'application recupere les classes auxquelles il appartient.

Ensuite, quand il ouvre un cours, le systeme verifie qu'il a bien le droit d'y acceder.

Quand l'eleve regarde une video, l'interface envoie la progression au controller.

Le systeme verifie le contenu, enregistre `progress_seconds`, puis renvoie une confirmation.

L'interet est de montrer que le parcours complet passe par l'interface, le controller, les policies, les modeles et la base de donnees.

Ce schema est aussi interessant parce qu'il montre que la sauvegarde de progression n'est pas juste un affichage cote front. Elle passe bien par le backend, avec une verification, puis une ecriture en base.

Concretement, si l'eleve regarde 10 minutes d'une video de 20 minutes, l'application peut enregistrer 600 secondes. La prochaine fois, on peut donc reprendre la video ou afficher un pourcentage d'avancement.

---

## Slide 10 - Wireframes eleve

Ici, on voit deux wireframes de l'espace eleve.

Le premier montre la page "mes cours". L'objectif est que l'eleve retrouve rapidement les cours auxquels il a acces.

Le deuxieme montre la lecture d'une video. C'est important parce que le projet suit la progression de l'eleve pendant la consultation.

Ces wireframes permettent de montrer que l'interface a ete pensee autour du parcours utilisateur, pas seulement autour de la base de donnees.

Pour un eleve, l'interface doit rester simple. Il n'a pas besoin de voir toute la complexite technique derriere. Il doit surtout comprendre : quels cours sont disponibles, ou il en est, et comment continuer.

C'est pour ca que les wireframes mettent en avant la liste des cours et la lecture du contenu.

---

## Slide 11 - Stack technique

La stack technique repose principalement sur Laravel.

Laravel gere le backend, les routes, les controllers, les modeles Eloquent et les policies.

API Platform sert a exposer les ressources en API REST.

Pour l'interface, j'utilise Blade, TailwindCSS et un peu d'Alpine.js.

Vite sert a compiler les assets front, et PHPUnit sert pour les tests.

La base de donnees est creee avec les migrations Laravel.

J'ai choisi cette stack parce qu'elle reste assez coherente : Laravel fournit deja beaucoup d'outils pour l'authentification, les routes, la base de donnees et les tests. API Platform vient completer avec une API REST documentee.

Cela permet d'avoir a la fois une interface web classique et une API, ce qui rend le projet plus evolutif.

---

## Slide 12 - Architecture applicative

Cette slide resume le fonctionnement global.

L'utilisateur utilise le navigateur. Ensuite, les requetes arrivent sur les routes web ou les routes API.

Si c'est une page web, elle passe par un controller Blade.

Si c'est une requete API, elle passe par API Platform.

Dans les deux cas, les droits sont controles par les policies, puis les donnees sont manipulees avec les modeles Eloquent.

Enfin, les donnees sont stockees dans la base.

Un point important est que les controles de droits sont cote serveur. Meme si un utilisateur essaye d'appeler une route directement, Laravel doit quand meme verifier ses droits.

C'est pour ca que les policies sont placees au centre de l'architecture. Elles evitent de se reposer uniquement sur ce que l'interface affiche ou cache.

---

## Slide 13 - Routes et API

Ici, on voit un extrait des routes.

Les routes sont protegees par `auth`, donc il faut etre connecte.

Ensuite, chaque espace est protege par un middleware de role : admin, prof ou eleve.

Cela permet d'eviter qu'un eleve accede directement a l'espace professeur ou administrateur.

En dessous, on voit aussi les ressources exposees par l'API, comme les utilisateurs, les classes, les cours, les chapitres, les contenus et les progressions.

L'API est utile pour manipuler les donnees de maniere standard. Par exemple, on peut recuperer la liste des cours, creer un chapitre, ou mettre a jour une progression.

La documentation Swagger permet aussi de tester les endpoints plus facilement et de comprendre les donnees attendues.

---

## Slide 14 - Events et listeners

Cette partie montre la logique evenementielle.

Quand une action importante se produit, on peut declencher un evenement.

Par exemple, quand un eleve est inscrit dans une classe, on peut declencher `UserEnrolledInClass`.

Quand une progression est modifiee, on peut declencher `ContentProgressUpdated`.

L'avantage, c'est que le code principal reste plus propre. On peut ajouter plus tard des logs, des statistiques ou des notifications sans tout modifier.

Par exemple, aujourd'hui un evenement peut simplement ecrire un log ou mettre a jour une statistique. Demain, on pourrait ajouter une notification email au professeur sans changer toute la logique de progression.

C'est une facon de rendre le projet plus extensible.

---

## Slide 15 - Base de donnees / MPD

Ce schema presente le MPD, donc la structure physique de la base de donnees.

On retrouve les tables principales : `users`, `school_classes`, `courses`, `chapters`, `chapter_contents`, `user_content_progress` et `file_metadata`.

La relation importante est la progression : elle relie un utilisateur a un contenu de chapitre.

Il y a aussi une table pivot `class_user` pour gerer les inscriptions des eleves dans les classes.

Ce schema montre que les donnees sont bien reliees entre elles.

Ce qui est important dans ce MPD, c'est qu'on ne stocke pas les donnees de maniere isolee. Par exemple, un contenu appartient a un chapitre, un chapitre appartient a un cours, et un cours appartient a une classe.

Cela permet de repondre a des questions metier simples : quels cours un eleve peut-il voir ? Quels contenus appartiennent a ce cours ? Quelle est sa progression sur chaque contenu ?

---

## Slide 16 - Schema de classes

Le diagramme de classes montre les modeles principaux cote application.

On voit que `User` peut etre professeur, administrateur ou eleve.

Un professeur peut enseigner plusieurs cours. Une classe contient plusieurs cours. Un cours contient plusieurs chapitres, et un chapitre contient plusieurs contenus.

La classe `UserContentProgress` sert a enregistrer la progression d'un eleve sur un contenu.

On voit aussi `FileMetadata`, qui permet d'associer des fichiers pedagogiques aux cours, chapitres ou contenus.

Ce diagramme permet de faire le lien entre la conception et le code Laravel. Les classes du schema correspondent aux modeles Eloquent que l'on retrouve dans le projet.

L'avantage d'Eloquent est que les relations deviennent plus simples a utiliser dans le code. Au lieu de faire des requetes SQL partout, on peut passer par les relations entre modeles.

---

## Slide 17 - Contraintes importantes

Cette slide resume les contraintes principales de la base.

La table `class_user` gere la relation many-to-many entre les eleves et les classes.

La table `courses` relie un cours a une classe et a un professeur.

Les chapitres et les contenus ont une position pour garder un ordre logique.

La table `user_content_progress` doit etre unique pour un couple utilisateur/contenu, pour eviter d'avoir plusieurs progressions pour la meme video.

Enfin, `file_metadata` contient les informations sur les fichiers et leur niveau d'acces.

Ces contraintes sont importantes pour eviter les incoherences. Par exemple, si on n'avait pas d'unicite sur la progression, un meme eleve pourrait avoir plusieurs lignes pour la meme video, et le calcul deviendrait faux.

De la meme facon, la table pivot `class_user` permet de gerer proprement le cas ou un eleve appartient a plusieurs classes ou groupes.

---

## Slide 18 - Developpement / modeles principaux

Dans le developpement, les modeles principaux reprennent la logique metier.

Il y a trois roles : admin, professeur et eleve.

La structure pedagogique suit cinq niveaux : classe, cours, chapitre, contenu et progression.

Les ressources sont aussi exposees en REST avec API Platform.

L'objectif etait d'avoir une structure claire, qui ressemble au besoin fonctionnel.

Ce point est important parce que le code doit rester comprehensible. Si on regarde le besoin fonctionnel, on parle de classes, de cours, de chapitres, de contenus et de progression. Dans le code, on retrouve la meme logique.

Cela rend le projet plus facile a maintenir ou a expliquer a quelqu'un d'autre.

---

## Slide 19 - Exemple de code : modeles

Ici, on voit un extrait du modele `Course`.

Les champs remplissables sont le titre, la description, la classe associee et le professeur.

Ensuite, on voit deux relations Eloquent.

`schoolClass()` indique qu'un cours appartient a une classe.

`chapters()` indique qu'un cours peut contenir plusieurs chapitres.

Ces relations permettent d'ecrire du code plus simple, par exemple recuperer directement les chapitres d'un cours.

Par exemple, si je veux afficher la page d'un cours, je peux charger le cours puis ses chapitres. Laravel sait faire le lien grace aux relations Eloquent.

C'est aussi utile pour les tests, parce qu'on peut verifier que les relations entre les donnees sont correctes.

---

## Slide 20 - API : validation et synchronisation

Cette slide montre deux points importants.

D'abord, la validation API. Par exemple, lors de la creation d'un cours, le titre est obligatoire, et la classe doit exister.

Ensuite, la synchronisation des eleves avec `sync()`.

Au lieu de stocker une liste d'eleves dans une colonne, Laravel gere proprement la table pivot.

Cela evite les doublons et garde une base de donnees relationnelle propre.

Cette partie a ete un point important du projet, parce que la gestion des eleves dans une classe peut vite devenir confuse si on la fait manuellement.

Avec `sync()`, on dit clairement : voici la liste actuelle des eleves de la classe. Laravel met a jour la table pivot en consequence.

---

## Slide 21 - Fichiers et interface prof

Le projet gere aussi les fichiers pedagogiques.

Un professeur peut ajouter des fichiers comme des PDF, des videos, des images ou des documents.

Chaque fichier a un niveau d'acces : prive, classe ou public.

Il y a aussi un compteur de telechargements.

Sur la partie droite, on voit un wireframe de l'interface professeur, avec l'organisation des contenus d'un cours.

La gestion des fichiers ajoute aussi une problematique de securite. Un fichier peut etre public, reserve a une classe, ou prive. Il ne suffit donc pas de stocker le fichier, il faut aussi verifier qui peut le consulter ou le telecharger.

---

## Slide 22 - Wireframes professeur

Ces wireframes montrent l'espace professeur.

Le tableau de bord permet d'avoir une vue d'ensemble sur ses classes et ses cours.

La vue matiere permet de gerer un cours plus en detail : chapitres, contenus, videos et fichiers.

L'objectif est de rendre le parcours professeur assez simple : il doit pouvoir creer et organiser ses supports sans manipuler directement la base de donnees.

Pour le professeur, l'interface doit permettre de se concentrer sur le contenu pedagogique. Il cree un cours, ajoute un chapitre, ajoute une video ou un document, et l'application se charge de ranger correctement les donnees.

---

## Slide 23 - Securite / RBAC

La securite repose d'abord sur les roles.

Dans le modele `User`, on retrouve les constantes `admin`, `prof` et `eleve`.

La methode `hasRole()` permet de verifier le role d'un utilisateur.

Ensuite, chaque role donne acces a des parties differentes.

L'administrateur a une vue globale, le professeur gere ses propres ressources, et l'eleve consulte uniquement ses cours et sa progression.

Ce modele RBAC evite de melanger les responsabilites. Un eleve ne doit pas pouvoir creer un cours, et un professeur ne doit pas forcement pouvoir modifier les cours d'un autre professeur.

Dans une application scolaire, c'est essentiel parce qu'on manipule des donnees d'utilisateurs, des contenus pedagogiques et des progressions.

---

## Slide 24 - Policies

Les policies permettent de securiser les actions.

Par exemple, dans l'extrait, seuls un administrateur ou un professeur peuvent creer une ressource.

Cela empeche un eleve de creer un cours ou de modifier des donnees qui ne lui appartiennent pas.

Les tests verifient aussi ces regles, notamment avec des reponses 403 quand l'action est interdite.

C'est important parce que la securite ne doit pas dependre uniquement de ce qui est affiche dans l'interface.

Par exemple, meme si le bouton "creer un cours" n'apparait pas pour un eleve, il pourrait essayer d'envoyer une requete directement. La policy permet de bloquer cette action cote serveur.

Donc l'interface aide l'utilisateur, mais la vraie securite est dans le backend.

---

## Slide 25 - Securite fichiers et production

Cette slide resume la securite autour des fichiers et de l'API.

Un fichier prive est accessible au proprietaire ou a un administrateur.

Un fichier de classe est accessible aux utilisateurs inscrits dans la classe.

Un fichier public est plus largement accessible selon les regles prevues.

Pour la production, il est aussi possible de renforcer l'API avec `API_REQUIRE_AUTH=true`.

Et Swagger peut etre desactive en production pour eviter d'exposer la documentation si ce n'est pas souhaite.

Cette slide montre aussi qu'il faut penser a la difference entre developpement et production. En developpement, Swagger est pratique pour tester. En production, on peut preferer le limiter ou le desactiver.

De la meme facon, l'acces aux fichiers doit toujours passer par une verification, surtout si les documents sont reserves a une classe.

---

## Slide 26 - Tests

La strategie de test couvre plusieurs niveaux.

Les tests unitaires verifient la logique des modeles et les relations Eloquent.

Les tests feature verifient les routes, les vues et les uploads.

Les tests API verifient la validation, le CRUD et les formats acceptes.

Les tests de securite verifient que les roles sont bien respectes.

Enfin, les tests d'integration verifient un parcours complet.

L'objectif des tests n'est pas seulement de dire que le code fonctionne une fois. C'est aussi de pouvoir modifier le projet ensuite sans casser une fonctionnalite importante.

Par exemple, si je change une policy ou une relation Eloquent, les tests peuvent m'indiquer rapidement si j'ai casse l'acces d'un eleve a ses cours.

---

## Slide 27 - Parcours complet teste

Ici, on resume le test le plus important.

On cree d'abord un administrateur, un professeur et une classe.

Le professeur cree un cours, des chapitres et des contenus.

Ensuite, un eleve est inscrit dans la classe.

L'eleve consulte le cours, regarde une video, et sa progression est mise a jour.

Le test verifie aussi que les autres eleves n'ont pas acces a sa progression.

Ce test montre que les differentes parties du projet fonctionnent ensemble.

C'est probablement le test le plus representatif du projet, parce qu'il reproduit un scenario realiste.

Il ne teste pas seulement une table ou une route. Il teste l'enchainement complet : creation des utilisateurs, creation des cours, inscription de l'eleve, consultation du contenu, sauvegarde de la progression et verification des droits.

C'est ce qui donne confiance dans la coherence globale de l'application.

---

## Slide 28 - Deploiement / lancement local

Pour lancer le projet en local, il faut installer les dependances PHP avec Composer et les dependances front avec npm.

Ensuite, on cree le fichier `.env`, on genere la cle Laravel, puis on lance les migrations avec les seeders.

On peut demarrer Laravel avec `php artisan serve`.

Et pour le front, on lance `npm run dev`.

Les points importants a configurer sont la base de donnees, le stockage des fichiers, l'URL de l'application et les options API.

Cette etape montre que le projet peut etre installe de maniere reproductible. Les migrations permettent de reconstruire la structure de la base, et les seeders peuvent ajouter des donnees de depart.

C'est pratique pour un developpeur qui reprend le projet ou pour preparer une demonstration.

---

## Slide 29 - Mise en production

Pour la production, il faut optimiser l'installation.

On installe les dependances sans les dependances de developpement.

On compile le front avec `npm run build`.

On lance les migrations avec `--force`, puis on met en cache la configuration, les routes et les vues.

Le build Vite est tres important, parce que sans lui l'interface peut apparaitre sans style ou avec des composants mal affiches.

En production, on ne lance pas l'application exactement comme en developpement. On optimise les dependances, on compile les fichiers front et on met en cache certaines parties de Laravel.

Cela permet d'avoir une application plus stable et plus rapide.

---

## Slide 30 - Points DevOps retenus

Dans ce projet, il y a plusieurs points DevOps importants.

Les migrations permettent de versionner la base de donnees.

Les seeders permettent d'avoir des donnees de demonstration.

Les tests automatises permettent de verifier que les fonctionnalites restent correctes.

Les logs et les events aident a suivre ce qui se passe dans l'application.

Enfin, la separation de la configuration dans `.env` et le build front rendent le projet plus facile a livrer.

Pour moi, la partie DevOps ne se limite pas a "mettre en ligne". Elle commence deja dans la facon de structurer le projet : migrations, tests, configuration separee, commandes reproductibles et documentation.

Ce sont des elements qui facilitent la maintenance du projet.

---

## Slide 31 - Bilan

Pour conclure, Seira est une application Laravel complete qui repond a un besoin scolaire concret.

Elle propose un parcours pour trois roles : administrateur, professeur et eleve.

Techniquement, le projet utilise Laravel, Eloquent, API Platform, les policies, les events et les tests.

Les ameliorations possibles seraient d'ajouter des statistiques plus avancees, des notifications email, un export des progressions, une vraie CI/CD et un design system plus complet.

Merci pour votre attention. Je suis pret a repondre a vos questions.

Pour resumer en une phrase : Seira montre comment on peut construire une plateforme scolaire avec Laravel, en reliant le besoin fonctionnel, la conception, la securite, les tests et la logique de deploiement.
