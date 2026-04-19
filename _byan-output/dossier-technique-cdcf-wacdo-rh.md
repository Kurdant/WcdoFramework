# Dossier Technique & Cahier des Charges Fonctionnel
## Application de Gestion RH — Wacdo
### Bloc 3 — Développement avancé via Framework (RNCP Niveau 5)

---

> **Candidat :** Hugo  
> **Examen :** Titre Professionnel Développeur Web et Web Mobile — Bloc 3  
> **Date :** Avril 2026  
> **Application :** WacdoRH — Back-office de gestion des ressources humaines  
> **Stack :** Symfony 7.4 · PHP 8.3 (runtime Docker, `composer.json` requiert `>=8.2`) · Doctrine ORM 3 · Twig 3 · MariaDB 10.11 · Docker  

---

## Table des matières

**Partie I — Cahier des Charges Fonctionnel (CDCF)**
1. [Contexte et présentation](#1-contexte-et-présentation)
2. [Problématique](#2-problématique)
3. [Expression du besoin](#3-expression-du-besoin)
4. [Contraintes](#4-contraintes)
5. [Solution retenue](#5-solution-retenue)
6. [Périmètre fonctionnel](#6-périmètre-fonctionnel)

**Partie II — Dossier Technique**
7. [Benchmark et choix technologiques](#7-benchmark-et-choix-technologiques)
8. [Architecture de l'application](#8-architecture-de-lapplication)
9. [Modèle de données](#9-modèle-de-données)
10. [Description des fonctionnalités](#10-description-des-fonctionnalités)
11. [Sécurité](#11-sécurité)
12. [Infrastructure Docker](#12-infrastructure-docker)
13. [Méthode de gestion de projet](#13-méthode-de-gestion-de-projet)
14. [Tests et validation](#14-tests-et-validation)
15. [Livrables et déploiement](#15-livrables-et-déploiement)

---

---

# PARTIE I — CAHIER DES CHARGES FONCTIONNEL

---

## 1. Contexte et présentation

### 1.1 Présentation du commanditaire

**Wacdo** est une chaîne de restauration rapide exploitant plusieurs points de vente en France. L'entreprise emploie des centaines de collaborateurs répartis sur différents restaurants, occupant des postes variés : équipiers, caissiers, managers, directeurs, préparateurs.

Dans le cadre de l'expansion de ses activités, la direction des ressources humaines de Wacdo a exprimé le besoin de disposer d'un outil centralisé pour gérer les affectations de ses collaborateurs.

### 1.2 Contexte de l'examen

Ce projet s'inscrit dans le cadre du **Bloc 3 — Développement avancé via Framework** de l'examen du Titre Professionnel Développeur Web et Web Mobile (RNCP Niveau 5). Le candidat a choisi le **Sujet 1 : Framework Back**, qui demande de concevoir et développer une application complète en utilisant un framework PHP côté serveur.

Ce bloc fait suite au Bloc 1 (front-end natif) et au Bloc 2 (back-end PHP natif MVC), et démontre la maîtrise d'un framework professionnel — niveau supplémentaire de complexité et de structuration du code.


**Bloc 1**
  - *Technologie* : HTML / CSS / JavaScript natif
  - *Périmètre* : Interface borne de commande

**Bloc 2**
  - *Technologie* : PHP natif MVC + PDO + API REST
  - *Périmètre* : Back-office commandes

****Bloc 3****
  - *Technologie* : **Symfony 7 + Doctrine + Twig**
  - *Périmètre* : **Application RH (ce projet)**


---

## 2. Problématique

Wacdo ne dispose pas d'outil dédié à la gestion des ressources humaines opérationnelles. Les problèmes identifiés sont les suivants :

- **Aucune traçabilité** des affectations : il est difficile de savoir quel collaborateur était en poste dans quel restaurant, et à quelle période.
- **Pas de vision claire des effectifs en temps réel** : impossible de savoir rapidement qui est actuellement en poste dans chaque restaurant.
- **Gestion manuelle et error-prone** : les informations sont éparpillées (tableurs, papier), sources d'erreurs et de doublons.
- **Pas de contrôle d'accès** : n'importe qui pourrait consulter ou modifier des données sensibles.

**Question centrale :** Comment centraliser la gestion des collaborateurs, des restaurants et de leurs affectations dans un outil sécurisé, accessible uniquement aux administrateurs autorisés ?

---

## 3. Expression du besoin

### 3.1 Acteurs


****Administrateur****
  - *Description* : Collaborateur Wacdo avec le flag `administrateur = true` et un mot de passe
  - *Droits* : Accès complet à toutes les fonctionnalités

****Collaborateur standard****
  - *Description* : Collaborateur sans droit admin
  - *Droits* : Aucun accès à l'application

****Visiteur anonyme****
  - *Description* : Non authentifié
  - *Droits* : Redirigé vers la page de connexion


### 3.2 Besoins fonctionnels

**BF-01 — Authentification**
L'application ne doit être accessible qu'aux administrateurs identifiés. L'accès se fait via un formulaire email + mot de passe.

**BF-02 — Gestion des restaurants**
- Consulter la liste des restaurants avec filtres (nom, code postal, ville)
- Créer, modifier un restaurant
- Voir le détail d'un restaurant avec la liste des collaborateurs actuellement en poste
- Consulter l'historique complet des affectations d'un restaurant
- Affecter un nouveau collaborateur à un restaurant

**BF-03 — Gestion des collaborateurs**
- Consulter la liste des collaborateurs avec filtres (nom, prénom, email)
- Créer, modifier un collaborateur
- Voir le détail d'un collaborateur avec ses affectations en cours et son historique
- Identifier les collaborateurs non affectés
- Affecter un collaborateur à un nouveau poste

**BF-04 — Gestion des fonctions**
- Consulter la liste des fonctions (postes existants chez Wacdo)
- Créer et modifier une fonction

**BF-05 — Recherche des affectations**
- Afficher toutes les affectations avec filtres (poste, dates, ville)
- Distinguer les affectations actives (sans date de fin) des affectations terminées

**BF-06 — Validation des données**
- Empêcher l'enregistrement de données incomplètes ou invalides
- Afficher des messages d'erreur clairs en cas de saisie incorrecte

### 3.3 Besoins non fonctionnels


**BNF-01**
  - *Besoin* : Sécurité
  - *Description* : Authentification robuste, mots de passe hachés, protection CSRF

**BNF-02**
  - *Besoin* : Maintenabilité
  - *Description* : Code structuré selon les conventions Symfony (MVC, DI, conventions de nommage)

**BNF-03**
  - *Besoin* : Portabilité
  - *Description* : Application conteneurisée via Docker, déployable sur tout environnement

**BNF-04**
  - *Besoin* : Testabilité
  - *Description* : Tests unitaires et fonctionnels couvrant les composants critiques

**BNF-05**
  - *Besoin* : Ergonomie
  - *Description* : Interface responsive Bootstrap 5, navigation claire


---

## 4. Contraintes

### 4.1 Contraintes techniques (imposées par le sujet d'examen)

- ****Framework back**** — Utilisation obligatoire d'un framework PHP côté serveur
- ****ORM**** — Gestion des entités via un ORM (Doctrine)
- ****Moteur de templates**** — Utilisation obligatoire d'un moteur de templates (Twig)
- ****Base de données SQL**** — MySQL / MariaDB
- ****Sécurité**** — Authentification et autorisation des utilisateurs obligatoires
- ****Tests**** — Tests d'interface, fonctionnels et de sécurité requis


### 4.2 Contraintes projet

- ****Isolement**** — L'application Bloc 3 est 100% indépendante des Blocs 1 et 2
- ****Base de données séparée**** — Base `wcdo_rh` dédiée, distincte de la base `wcdo` des Blocs 1/2
- ****Environnement conteneurisé**** — Docker obligatoire pour la portabilité
- ****PHP 8.3**** — Version moderne du langage


### 4.3 Contraintes métier

- Seuls les collaborateurs avec le flag `administrateur = true` peuvent se connecter
- Un collaborateur non-admin peut exister dans la BDD (il est géré) mais ne peut pas accéder à l'application
- Une affectation sans date de fin est considérée active
- L'email d'un collaborateur est unique

---

## 5. Solution retenue

### 5.1 Décision

Développement d'une **application web back-office monolithique** en **Symfony 7.4**, avec rendu côté serveur (SSR) via **Twig**, persistance via **Doctrine ORM** sur **MariaDB**, le tout conteneurisé sous **Docker**.

### 5.2 Justification du choix Symfony vs autres frameworks


**Popularité en France**
  - *Symfony 7* : Leader (3/3)
  - *Laravel* : Fort (2/3)
  - *PHP natif (Bloc 2)* : N/A

**Courbe d'apprentissage**
  - *Symfony 7* : Moyenne
  - *Laravel* : Douce
  - *PHP natif (Bloc 2)* : Déjà maîtrisé

**Composants intégrés**
  - *Symfony 7* : Tous (Security, Form, Validator…)
  - *Laravel* : Équivalents
  - *PHP natif (Bloc 2)* : À coder soi-même

**ORM natif**
  - *Symfony 7* : Doctrine
  - *Laravel* : Eloquent
  - *PHP natif (Bloc 2)* : PDO manuel

**Conventions strictes**
  - *Symfony 7* : Oui (+ pédagogique pour jury)
  - *Laravel* : Moins
  - *PHP natif (Bloc 2)* : N/A

**LTS / Stabilité**
  - *Symfony 7* : Oui (Symfony 7.4 LTS)
  - *Laravel* : Oui
  - *PHP natif (Bloc 2)* : N/A

**Adéquation examen**
  - *Symfony 7* : **Référence en formation FR**
  - *Laravel* : Bon
  - *PHP natif (Bloc 2)* : Insuffisant pour le bloc


**Symfony** a été retenu car c'est le framework de référence en France pour la formation développeur web, avec un écosystème de composants riche et des conventions claires qui démontrent la maîtrise d'un vrai framework professionnel.

### 5.3 Ce que le framework apporte vs PHP natif (Bloc 2)

- **`Router.php` custom (200 lignes)** — Composant Routing (attributs PHP 8)
- **`$_POST` lu manuellement** — FormType avec binding automatique
- **`password_hash()` manuel** — `UserPasswordHasherInterface`
- **`$_SESSION` géré à la main** — Firewall Symfony + Security Component

| SQL PDO `$stmt->prepare()` | Doctrine QueryBuilder + EntityManager |
- **Validation manuelle de chaque champ** — Attributs Assert sur les entités


---

## 6. Périmètre fonctionnel

### 6.1 Fonctionnalités incluses (IN SCOPE)

- Authentification par email + mot de passe (administrateurs uniquement)
- CRUD Restaurants avec filtres et détail
- CRUD Collaborateurs avec filtres, détail et historique
- CRUD Fonctions
- CRUD Affectations avec filtres multiparamètres
- Vue collaborateurs non affectés
- Affectation depuis la fiche restaurant ET depuis la fiche collaborateur
- Historique des affectations (actives et terminées)
- Validation serveur de tous les formulaires
- Protection CSRF
- Données de démonstration (fixtures)
- Tests unitaires et fonctionnels PHPUnit

### 6.2 Hors périmètre (OUT OF SCOPE)

- Interface publique (pas d'accès non-admin)
- Gestion des congés / absences
- Notifications email
- Export PDF / Excel
- API REST (pas de consommation front séparée)
- Pagination (non exigée par le sujet)

---

---

# PARTIE II — DOSSIER TECHNIQUE

---

## 7. Benchmark et choix technologiques

### 7.1 Framework PHP

#### Comparatif des frameworks back PHP


**Type**
  - *Symfony 7* : Full-stack
  - *Laravel 11* : Full-stack
  - *CodeIgniter 4* : Full-stack
  - *Slim 4* : Micro

**ORM intégré**
  - *Symfony 7* : Doctrine (externe mais standard)
  - *Laravel 11* : Eloquent (intégré)
  - *CodeIgniter 4* : Léger (intégré)
  - *Slim 4* : Aucun

**Sécurité**
  - *Symfony 7* : Composant dédié (3/3)
  - *Laravel 11* : Guards (3/3)
  - *CodeIgniter 4* : Basique (2/3)
  - *Slim 4* : Manuel (1/3)

**Formulaires**
  - *Symfony 7* : FormType complet (3/3)
  - *Laravel 11* : Form Request (2/3)
  - *CodeIgniter 4* : Basique (2/3)
  - *Slim 4* : Aucun composant natif

**Twig natif**
  - *Symfony 7* : Oui
  - *Laravel 11* : Non (Blade)
  - *CodeIgniter 4* : Non
  - *Slim 4* : Non

**Popularité France**
  - *Symfony 7* : 1er
  - *Laravel 11* : 2e
  - *CodeIgniter 4* : 3e
  - *Slim 4* : Niche

**Usage formation FR**
  - *Symfony 7* : Référence
  - *Laravel 11* : Répandu
  - *CodeIgniter 4* : Minoritaire
  - *Slim 4* : Rare


**Choix retenu : Symfony 7.4** — Standard de l'industrie en France, composants matures, idéal pour démontrer la maîtrise d'un framework complet.

### 7.2 ORM


**Mapping objet-relationnel**
  - *Doctrine ORM* : Complet
  - *Eloquent* : Complet
  - *PDO natif (Bloc 2)* : Manuel

**Migrations**
  - *Doctrine ORM* : Automatiques
  - *Eloquent* : Automatiques
  - *PDO natif (Bloc 2)* : Manuel

**QueryBuilder**
  - *Doctrine ORM* : Puissant
  - *Eloquent* : Fluent
  - *PDO natif (Bloc 2)* : N/A

**Relations**
  - *Doctrine ORM* : OneToMany / ManyToOne / ManyToMany
  - *Eloquent* : Idem
  - *PDO natif (Bloc 2)* : Requêtes manuelles

**Intégration Symfony**
  - *Doctrine ORM* : Native
  - *Eloquent* : Addon
  - *PDO natif (Bloc 2)* : N/A


**Choix retenu : Doctrine ORM 3** — Standard de facto avec Symfony, attributs PHP 8 modernes.

### 7.3 Moteur de templates


**Héritage de templates**
  - *Twig 3* : Oui (`extends`/`block`)
  - *Blade (Laravel)* : Oui
  - *PHP Natif* : Non (include seulement)

**Syntaxe sécurisée (auto-escape)**
  - *Twig 3* : Oui, par défaut
  - *Blade (Laravel)* : Oui
  - *PHP Natif* : Non (à faire manuellement)

**Intégration formulaires Symfony**
  - *Twig 3* : Native
  - *Blade (Laravel)* : N/A
  - *PHP Natif* : N/A

**Lisibilité**
  - *Twig 3* : (3/3)
  - *Blade (Laravel)* : (3/3)
  - *PHP Natif* : (2/3)

**Form themes Bootstrap 5**
  - *Twig 3* : Intégré
  - *Blade (Laravel)* : N/A
  - *PHP Natif* : N/A


**Choix retenu : Twig 3** — Natif Symfony, héritage de templates (`{% extends %}`), auto-escape XSS, thème Bootstrap 5 prêt à l'emploi.

### 7.4 Base de données


**Compatibilité dialecte MySQL**
  - *MariaDB 10.11* : Oui (fork quasi drop-in)
  - *PostgreSQL 16* : Non (dialecte PostgreSQL distinct)
  - *SQLite 3* : Partielle (SQL ANSI simplifié)

**Production ready**
  - *MariaDB 10.11* : Oui
  - *PostgreSQL 16* : Oui
  - *SQLite 3* : Oui (embarqué : mobile, Firefox, iOS, etc.) mais single-writer, peu adapté à un back-office concurrent

**Déjà en place sur l'infra Blocs 1/2**
  - *MariaDB 10.11* : Oui
  - *PostgreSQL 16* : Non
  - *SQLite 3* : N/A

**Performance (lecture/écriture concurrente)**
  - *MariaDB 10.11* : (3/3)
  - *PostgreSQL 16* : (3/3)
  - *SQLite 3* : (2/3)

**Image Docker officielle**
  - *MariaDB 10.11* : Oui (`mariadb`)
  - *PostgreSQL 16* : Oui (`postgres`)
  - *SQLite 3* : N/A (librairie embarquée)

**Support Doctrine DBAL**
  - *MariaDB 10.11* : Oui (driver `pdo_mysql`)
  - *PostgreSQL 16* : Oui (driver `pdo_pgsql`)
  - *SQLite 3* : Oui (driver `pdo_sqlite`)


**Choix retenu : MariaDB 10.11** — Déjà présente dans l'infrastructure Docker de la formation, compatible avec le dialecte MySQL imposé par le sujet, et adaptée aux accès concurrents d'une application multi-utilisateurs. Création d'une nouvelle base dédiée `wcdo_rh` isolée des bases `wcdo` des Blocs 1/2.

### 7.5 CSS / Frontend


**Rapidité d'intégration**
  - *Bootstrap 5 (CDN)* : (3/3)
  - *Tailwind CSS* : (2/3)
  - *CSS Custom* : (1/3)

**Composants prêts (tables, forms, badges)**
  - *Bootstrap 5 (CDN)* : Riche bibliothèque
  - *Tailwind CSS* : Partiel (Tailwind UI en option payante)
  - *CSS Custom* : À écrire manuellement

**Form theme Symfony natif**
  - *Bootstrap 5 (CDN)* : `bootstrap_5_layout.html.twig` fourni
  - *Tailwind CSS* : Non natif
  - *CSS Custom* : N/A

**Temps de développement**
  - *Bootstrap 5 (CDN)* : Très court
  - *Tailwind CSS* : Court
  - *CSS Custom* : Long

**Pertinence back-office**
  - *Bootstrap 5 (CDN)* : Bonne (composants admin classiques)
  - *Tailwind CSS* : Bonne (mais nécessite build Tailwind)
  - *CSS Custom* : Surqualité pour un back-office


**Choix retenu : Bootstrap 5 via CDN** — Back-office propre et responsive sans perdre de temps sur le CSS. Le focus du Bloc 3 est sur PHP/Symfony, pas sur le design.

### 7.6 Récapitulatif des dépendances Composer

> Contraintes de la plateforme (`composer.json`) : `php: >=8.2`, `ext-ctype: *`, `ext-iconv: *`.  
> Runtime réellement utilisé dans le conteneur : **PHP 8.3.30** (image `php:8.3-fpm-alpine`).

#### Dépendances de production (`require`)


**`doctrine/doctrine-bundle`**
  - *Version* : ^2.18
  - *Rôle* : Intégration Doctrine dans Symfony

**`doctrine/doctrine-migrations-bundle`**
  - *Version* : ^3.7
  - *Rôle* : Migrations de schéma

**`doctrine/orm`**
  - *Version* : ^3.6
  - *Rôle* : ORM Doctrine (mapping, EntityManager)

**`symfony/asset`**
  - *Version* : 7.4.*
  - *Rôle* : Helper `asset()` dans Twig

**`symfony/console`**
  - *Version* : 7.4.*
  - *Rôle* : Commandes `bin/console`

**`symfony/dotenv`**
  - *Version* : 7.4.*
  - *Rôle* : Chargement des fichiers `.env`

**`symfony/flex`**
  - *Version* : ^2
  - *Rôle* : Installateur de recipes Symfony

**`symfony/form`**
  - *Version* : 7.4.*
  - *Rôle* : FormType, rendu, validation

**`symfony/framework-bundle`**
  - *Version* : 7.4.*
  - *Rôle* : Bundle de base du framework

**`symfony/runtime`**
  - *Version* : 7.4.*
  - *Rôle* : Runtime découplé (FPM / CLI)

**`symfony/security-bundle`**
  - *Version* : 7.4.*
  - *Rôle* : Firewall, providers, hashers, rôles

**`symfony/security-csrf`**
  - *Version* : 7.4.*
  - *Rôle* : Protection CSRF des formulaires

**`symfony/twig-bundle`**
  - *Version* : 7.4.*
  - *Rôle* : Intégration Twig + thème Bootstrap 5

**`symfony/validator`**
  - *Version* : 7.4.*
  - *Rôle* : Contraintes `Assert` sur les entités

**`symfony/yaml`**
  - *Version* : 7.4.*
  - *Rôle* : Lecture des fichiers de configuration YAML

**`twig/extra-bundle`**
  - *Version* : ^2.12 \
  - *Rôle* : ^3.0

**`twig/twig`**
  - *Version* : ^2.12 \
  - *Rôle* : ^3.0


#### Dépendances de développement (`require-dev`)


**`doctrine/doctrine-fixtures-bundle`**
  - *Version* : ^4.3
  - *Rôle* : Chargement des fixtures (données de démo)

**`phpunit/phpunit`**
  - *Version* : ^12.5
  - *Rôle* : Framework de tests unitaires et fonctionnels

**`symfony/browser-kit`**
  - *Version* : 7.4.*
  - *Rôle* : Client HTTP pour `WebTestCase`

**`symfony/css-selector`**
  - *Version* : 7.4.*
  - *Rôle* : Sélecteurs CSS pour les assertions `Crawler`

**`symfony/debug-bundle`**
  - *Version* : 7.4.*
  - *Rôle* : Affichage amélioré des erreurs (dev)

**`symfony/maker-bundle`**
  - *Version* : ^1.67
  - *Rôle* : CLI `make:entity`, `make:controller`, …

**`symfony/stopwatch`**
  - *Version* : 7.4.*
  - *Rôle* : Mesure de performance pour le profiler

**`symfony/web-profiler-bundle`**
  - *Version* : 7.4.*
  - *Rôle* : Web Debug Toolbar et profiler (dev)


---

## 8. Architecture de l'application

### 8.1 Pattern architectural — MVC avec Symfony

L'application suit le pattern **MVC (Modèle-Vue-Contrôleur)** tel qu'implémenté par Symfony :

```
                    Requête HTTP
                         |
                         v
                +------------------+
                |     Kernel       |  <- Point d'entrée : public/index.php
                |     Symfony      |
                +--------+---------+
                         |
                         v
     +-------------+     +-----------------------------+
     |   Router    |---->|  Controller (C)             |
     |  (routing)  |     |  AbstractController         |
     +-------------+     |  Injection auto (DI)        |
                         +---+---------+---------+-----+
                             |         |         |
                             v         v         v
                    +--------+   +---------+   +--------+
                    | Entity |   |FormType |   |Template|
                    |  (M)   |   |         |   | Twig(V)|
                    +---+----+   +---------+   +--------+
                        |
                        v
                +---------------+
                |  Repository   |
                |  (Doctrine)   |
                +-------+-------+
                        |
                        v
                +---------------+
                |   MariaDB     |
                +---------------+
```

### 8.2 Arborescence du projet

```
symfony/
├── config/
│   ├── packages/
│   │   ├── security.yaml     <- Firewall, rôles, bcrypt
│   │   ├── doctrine.yaml     <- Connexion BDD, mapping
│   │   ├── twig.yaml         <- Thème Bootstrap 5
│   │   ├── csrf.yaml         <- Force le CSRF stateful (fix Symfony 7.4)
│   │   └── framework.yaml    <- Session, CSRF global
│   └── routes.yaml
│
├── src/
│   ├── Entity/               <- Modèles (M)
│   │   ├── Collaborateur.php <- UserInterface + PasswordAuthenticatedUserInterface
│   │   ├── Restaurant.php
│   │   ├── Fonction.php
│   │   └── Affectation.php
│   │
│   ├── Repository/           <- Accès données Doctrine
│   │   ├── CollaborateurRepository.php
│   │   ├── RestaurantRepository.php
│   │   ├── FonctionRepository.php
│   │   └── AffectationRepository.php
│   │
│   ├── Controller/           <- Contrôleurs (C)
│   │   ├── SecurityController.php
│   │   ├── DashboardController.php
│   │   ├── RestaurantController.php
│   │   ├── CollaborateurController.php
│   │   ├── FonctionController.php
│   │   └── AffectationController.php
│   │
│   ├── Form/                 <- FormTypes
│   │   ├── RestaurantType.php
│   │   ├── RestaurantFilterType.php
│   │   ├── CollaborateurType.php
│   │   ├── CollaborateurFilterType.php
│   │   ├── FonctionType.php
│   │   ├── AffectationType.php
│   │   └── AffectationFilterType.php
│   │
│   └── DataFixtures/
│       └── AppFixtures.php
│
├── templates/                <- Vues Twig (V) — 19 fichiers
│   ├── base.html.twig        <- Layout principal (navbar, flash)
│   ├── security/login.html.twig
│   ├── dashboard/index.html.twig
│   ├── restaurant/{index,new,show,edit,affecter}.html.twig
│   ├── collaborateur/{index,new,show,edit,affecter}.html.twig
│   ├── fonction/{index,new,edit}.html.twig
│   └── affectation/{index,new,edit}.html.twig
│
└── tests/
    ├── Entity/
    │   ├── CollaborateurTest.php
    │   └── AffectationTest.php
    └── Controller/
        └── SecurityControllerTest.php
```

### 8.3 Flux d'une requête type

Exemple : accès à `/restaurant` (liste des restaurants)

```
1. Navigateur → GET http://localhost:8090/restaurant
2. Nginx → PHP-FPM → public/index.php (Front Controller Symfony)
3. Kernel → Firewall Security : utilisateur connecté ? → Oui (ROLE_ADMIN)
4. Router → RestaurantController::index()
5. Controller → RestaurantRepository::findByFilters(nom, cp, ville)
6. Repository → Doctrine EntityManager → SQL → MariaDB → Résultats
7. Controller → render('restaurant/index.html.twig', ['restaurants' => $results])
8. Twig → compile template + inject data → HTML
9. Réponse HTTP 200 → Navigateur affiche la liste
```

---

## 9. Modèle de données

### 9.1 Modèle Conceptuel de Données (MCD)

```
+--------------------------------+        +-----------------------+
|         Collaborateur          |        |      Restaurant       |
+--------------------------------+        +-----------------------+
| id (PK)                        |        | id (PK)               |
| nom                            |        | nom                   |
| prenom                         |        | adresse               |
| email (UNIQUE)                 |        | code_postal           |
| date_embauche                  |        | ville                 |
| administrateur (bool)          |        +-----------+-----------+
| mot_de_passe (nullable)        |                    |
| roles (JSON)                   |                    |
+---------------+----------------+                    |
                |                                     |
              1 | N                               1 N |
                |                                     |
                v                                     v
              +------------------------------------------+
              |                Affectation               |
              +------------------------------------------+
              | id (PK)                                  |
              | date_debut                               |
              | date_fin (NULL => affectation active)    |
              | collaborateur_id (FK)                    |
              | restaurant_id   (FK)                     |
              | fonction_id     (FK)                     |
              +-------------------+----------------------+
                                  |
                              N 1 |
                                  v
                       +----------+----------+
                       |       Fonction      |
                       +---------------------+
                       | id (PK)             |
                       | intitule (UNIQUE)   |
                       +---------------------+
```

**Cardinalités :**
- `Collaborateur` **1 → N** `Affectation` : un collaborateur peut avoir plusieurs affectations dans le temps
- `Restaurant` **1 → N** `Affectation` : un restaurant accueille plusieurs collaborateurs
- `Fonction` **1 → N** `Affectation` : un même poste est occupé par plusieurs personnes
- **Affectation active** : `date_fin IS NULL`
- **Affectation terminée** : `date_fin IS NOT NULL`

### 9.2 Modèle Logique de Données (MLD)

```sql
collaborateur (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nom VARCHAR(100) NOT NULL,
 prenom VARCHAR(100) NOT NULL,
 email VARCHAR(180) NOT NULL UNIQUE,
 date_embauche DATE NOT NULL,
 administrateur TINYINT(1) NOT NULL DEFAULT 0,
 mot_de_passe VARCHAR(255) NULL,
 roles JSON NOT NULL
)

restaurant (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nom VARCHAR(150) NOT NULL,
 adresse VARCHAR(255) NOT NULL,
 code_postal VARCHAR(10) NOT NULL,
 ville VARCHAR(100) NOT NULL
)

fonction (
 id INT AUTO_INCREMENT PRIMARY KEY,
 intitule VARCHAR(100) NOT NULL UNIQUE
)

affectation (
 id INT AUTO_INCREMENT PRIMARY KEY,
 date_debut DATE NOT NULL,
 date_fin DATE NULL,
 collaborateur_id INT NOT NULL,
 restaurant_id INT NOT NULL,
 fonction_id INT NOT NULL,
 FOREIGN KEY (collaborateur_id) REFERENCES collaborateur(id),
 FOREIGN KEY (restaurant_id) REFERENCES restaurant(id),
 FOREIGN KEY (fonction_id) REFERENCES fonction(id),
 INDEX idx_date_fin (date_fin)
)
```

### 9.3 Entités Doctrine

#### `Collaborateur` — implémente `UserInterface`

L'entité `Collaborateur` implémente deux interfaces Symfony Security :
- `UserInterface` → fournit l'identifiant (`email`), les rôles, et l'effacement des credentials
- `PasswordAuthenticatedUserInterface` → fournit le mot de passe haché

```php
#[ORM\Entity(repositoryClass: CollaborateurRepository::class)]
class Collaborateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $nom = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $prenom = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;  // getUserIdentifier()

    private array $roles = [];      // JSON : ['ROLE_ADMIN'] ou ['ROLE_USER']
    private bool $administrateur = false;
    private ?string $motDePasse = null; // nullable : seuls les admins ont un mdp

    // Synchronisation automatique rôles <-> administrateur
    public function setAdministrateur(bool $val): static {
        $this->administrateur = $val;
        $this->roles = $val ? ['ROLE_ADMIN'] : ['ROLE_USER'];
        return $this;
    }
}
```

**Point clé** : `setAdministrateur(true)` injecte automatiquement `ROLE_ADMIN` dans le tableau `roles`. Le Security Component lit `getRoles()` pour autoriser l'accès.

### 9.4 Méthodes custom des Repositories


**`CollaborateurRepository`**
  - *Méthode* : `findByFilters(?nom, ?prenom, ?email)`
  - *Description* : LIKE conditionnel sur chaque champ

**`CollaborateurRepository`**
  - *Méthode* : `findNonAffectes()`
  - *Description* : LEFT JOIN affectations WITH `dateFin IS NULL`, WHERE affectation.id IS NULL → retourne les collaborateurs sans aucune affectation active

**`RestaurantRepository`**
  - *Méthode* : `findByFilters(?nom, ?cp, ?ville)`
  - *Description* : LIKE conditionnel

**`AffectationRepository`**
  - *Méthode* : `findByFilters(?fonction, ?dateDebut, ?dateFin, ?ville)`
  - *Description* : JOIN restaurant + conditions

**`AffectationRepository`**
  - *Méthode* : `findActivesByRestaurant($restaurant, ...)`
  - *Description* : WHERE date_fin IS NULL + filtres

**`AffectationRepository`**
  - *Méthode* : `findAllByRestaurant($restaurant, ...)`
  - *Description* : Toutes affectations d'un restaurant

**`AffectationRepository`**
  - *Méthode* : `findActivesByCollaborateur($collab)`
  - *Description* : Affectations actives d'un collaborateur

**`AffectationRepository`**
  - *Méthode* : `findAllByCollaborateur($collab, ...)`
  - *Description* : Historique complet d'un collaborateur


---

## 10. Description des fonctionnalités

### 10.1 Tableau de bord (Dashboard)

Page d'accueil après connexion. Affiche :
- Message de bienvenue avec le nom de l'administrateur connecté
- Statistiques rapides : nombre de restaurants, collaborateurs, fonctions, affectations actives
- Liens de navigation vers les 4 sections

**Route :** `GET /` → `DashboardController::index()`

### 10.2 Gestion des restaurants

#### Liste des restaurants — `GET /restaurant`
- Tableau listant tous les restaurants
- Formulaire de filtres (GET) : nom, code postal, ville
- Bouton « Nouveau restaurant »
- Lignes cliquables → fiche détail

#### Détail d'un restaurant — `GET /restaurant/{id}`
- Fiche : nom, adresse, code postal, ville
- Tableau des **collaborateurs en poste actuellement** (`dateFin IS NULL`)
- Filtres sur ce tableau : poste, nom collaborateur, date début
- Bouton « Modifier »

#### Modifier un restaurant — `GET|POST /restaurant/{id}/edit`
- Formulaire pré-rempli (nom, adresse, CP, ville)
- Tableau de l'**historique complet** des affectations (actives + terminées)
- Bouton « Affecter un nouveau collaborateur »

#### Affecter depuis un restaurant — `GET|POST /restaurant/{id}/affecter`
- Formulaire : sélection collaborateur + fonction + date début
- Le restaurant est pré-sélectionné (non modifiable)

### 10.3 Gestion des collaborateurs

#### Liste — `GET /collaborateur`
- Tableau : nom, prénom, email, date d'embauche, statut admin
- Filtres GET : nom, prénom, email
- Bouton « Créer » + bouton « Voir les non-affectés »

#### Non-affectés — `GET /collaborateur/non-affectes`
- Même template que la liste, filtre automatique sur collaborateurs sans affectation active
- Utilise `CollaborateurRepository::findNonAffectes()` (LEFT JOIN)

#### Détail — `GET /collaborateur/{id}`
- Fiche collaborateur
- Section **affectations en cours** avec lien Modifier sur chacune
- Section **historique** filtrable (poste, date)

#### Modifier — `GET|POST /collaborateur/{id}/edit`
- Formulaire modification + bouton « Affecter à un nouveau poste »
- Gestion conditionnelle du mot de passe (obligatoire si admin, null sinon)

### 10.4 Gestion des fonctions

- Liste simple avec bouton Créer et lien Modifier sur chaque ligne
- Formulaire minimal : champ `intitulé` unique
- Contrainte UNIQUE en BDD + Assert côté entité

### 10.5 Recherche des affectations

#### Liste — `GET /affectation`
- Tableau : collaborateur, restaurant, ville, fonction, date début, date fin, statut (badge coloré)
- Filtres GET : poste, date début, date fin, ville
- `AffectationRepository::findByFilters()` avec JOIN sur restaurant pour filtrer par ville

#### Modifier une affectation — `GET|POST /affectation/{id}/edit`
- Modifier date de fin (pour clôturer une affectation)
- Accessible depuis la fiche collaborateur (affectations en cours modifiables)

### 10.6 Récapitulatif des 20 routes


**1**
  - *Route* : `app_login`
  - *URL* : `/login`
  - *Méthodes* : GET, POST
  - *Controller* : SecurityController

**2**
  - *Route* : `app_logout`
  - *URL* : `/logout`
  - *Méthodes* : GET
  - *Controller* : SecurityController

**3**
  - *Route* : `app_dashboard`
  - *URL* : `/`
  - *Méthodes* : ANY
  - *Controller* : DashboardController

**4**
  - *Route* : `app_restaurant_index`
  - *URL* : `/restaurant`
  - *Méthodes* : GET
  - *Controller* : RestaurantController

**5**
  - *Route* : `app_restaurant_new`
  - *URL* : `/restaurant/new`
  - *Méthodes* : GET, POST
  - *Controller* : RestaurantController

**6**
  - *Route* : `app_restaurant_show`
  - *URL* : `/restaurant/{id}`
  - *Méthodes* : GET
  - *Controller* : RestaurantController

**7**
  - *Route* : `app_restaurant_edit`
  - *URL* : `/restaurant/{id}/edit`
  - *Méthodes* : GET, POST
  - *Controller* : RestaurantController

**8**
  - *Route* : `app_restaurant_affecter`
  - *URL* : `/restaurant/{id}/affecter`
  - *Méthodes* : GET, POST
  - *Controller* : RestaurantController

**9**
  - *Route* : `app_collaborateur_index`
  - *URL* : `/collaborateur`
  - *Méthodes* : GET
  - *Controller* : CollaborateurController

**10**
  - *Route* : `app_collaborateur_non_affectes`
  - *URL* : `/collaborateur/non-affectes`
  - *Méthodes* : GET
  - *Controller* : CollaborateurController

**11**
  - *Route* : `app_collaborateur_new`
  - *URL* : `/collaborateur/new`
  - *Méthodes* : GET, POST
  - *Controller* : CollaborateurController

**12**
  - *Route* : `app_collaborateur_show`
  - *URL* : `/collaborateur/{id}`
  - *Méthodes* : GET
  - *Controller* : CollaborateurController

**13**
  - *Route* : `app_collaborateur_edit`
  - *URL* : `/collaborateur/{id}/edit`
  - *Méthodes* : GET, POST
  - *Controller* : CollaborateurController

**14**
  - *Route* : `app_collaborateur_affecter`
  - *URL* : `/collaborateur/{id}/affecter`
  - *Méthodes* : GET, POST
  - *Controller* : CollaborateurController

**15**
  - *Route* : `app_fonction_index`
  - *URL* : `/fonction`
  - *Méthodes* : GET
  - *Controller* : FonctionController

**16**
  - *Route* : `app_fonction_new`
  - *URL* : `/fonction/new`
  - *Méthodes* : GET, POST
  - *Controller* : FonctionController

**17**
  - *Route* : `app_fonction_edit`
  - *URL* : `/fonction/{id}/edit`
  - *Méthodes* : GET, POST
  - *Controller* : FonctionController

**18**
  - *Route* : `app_affectation_index`
  - *URL* : `/affectation`
  - *Méthodes* : GET
  - *Controller* : AffectationController

**19**
  - *Route* : `app_affectation_new`
  - *URL* : `/affectation/new`
  - *Méthodes* : GET, POST
  - *Controller* : AffectationController

**20**
  - *Route* : `app_affectation_edit`
  - *URL* : `/affectation/{id}/edit`
  - *Méthodes* : GET, POST
  - *Controller* : AffectationController


---

## 11. Sécurité

### 11.1 Architecture d'authentification

```
     Navigateur
         |
         | 1. GET /login
         v
 +----------------------------------------+
 | SecurityController::login()            |
 | -> affiche login.html.twig (CSRF token)|
 +----------------------------------------+
         |
         | 2. POST /login (email + mdp + _csrf_token)
         |    (intercepté par le Firewall "main")
         v
 +----------------------------------------+
 | Symfony Security Firewall              |
 |                                        |
 |  1. EntityProvider -> charge           |
 |     Collaborateur par email            |
 |     (getUserIdentifier)                |
 |                                        |
 |  2. UserPasswordHasher                 |
 |     -> password_verify() (bcrypt)      |
 |                                        |
 |  3. access_control vérifie que         |
 |     ROLE_ADMIN est présent dans        |
 |     getRoles()                         |
 +----------------------------------------+
         |
         +-- OK   -> Token session -> Redirect `/`
         +-- Fail -> Redirect `/login` + flash error
```

### 11.2 Configuration security.yaml

```yaml
security:
    password_hashers:
        App\Entity\Collaborateur:
            algorithm: bcrypt
            cost: 13                    # Facteur de coût bcrypt élevé (OWASP recommande >=12)

    providers:
        app_user_provider:
            entity:
                class: App\Entity\Collaborateur
                property: email         # Identifiant de connexion

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|assets|build)/
            security: false

        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: app_login
                check_path: app_login
                default_target_path: app_dashboard
                enable_csrf: true       # Token CSRF sur le formulaire de login
            logout:
                path: app_logout
                target: app_login

    access_control:
        - { path: ^/login$, roles: PUBLIC_ACCESS }
        - { path: ^/, roles: ROLE_ADMIN }   # Tout le reste = admin requis

# Environnement de test : hashers réduits pour accélérer les tests PHPUnit
when@test:
    security:
        password_hashers:
            App\Entity\Collaborateur:
                algorithm: auto
                cost: 4
                time_cost: 3
                memory_cost: 10
```

> Un fichier `config/packages/csrf.yaml` complète cette configuration en forçant le **CSRF stateful** (`stateless_token_ids: []`). Symfony 7.4 installe par défaut un CSRF stateless incompatible avec `form_login`, ce qui empêche l'authentification. Ce correctif était indispensable.

### 11.3 Comparaison sécurité Bloc 2 vs Bloc 3


**Hash mdp**
  - *Bloc 2 (PHP natif)* : `password_hash(PASSWORD_BCRYPT)`
  - *Bloc 3 (Symfony)* : `UserPasswordHasherInterface::hashPassword()`

**Vérification**
  - *Bloc 2 (PHP natif)* : `password_verify()` manuel
  - *Bloc 3 (Symfony)* : Automatique via Firewall

**Session**
  - *Bloc 2 (PHP natif)* : `$_SESSION['user']` manuel
  - *Bloc 3 (Symfony)* : Token Symfony Security

**CSRF**
  - *Bloc 2 (PHP natif)* : Généré/vérifié manuellement
  - *Bloc 3 (Symfony)* : Automatique sur tous FormTypes

**Accès restreint**
  - *Bloc 2 (PHP natif)* : `if(!$_SESSION['admin']) redirect()`
  - *Bloc 3 (Symfony)* : `access_control` dans security.yaml


### 11.4 Validation des données

Implémentée sur 3 niveaux :

**Niveau 1 — Entités (annotations PHP 8) :**
```php
#[Assert\NotBlank]
#[Assert\Email]
private string $email;

#[Assert\Regex(pattern: '/^\d{5}$/', message: 'Code postal invalide')]
private string $codePostal;
```

**Niveau 2 — Formulaires (FormType) :**
```php
// handleRequest() déclenche la validation automatique
if ($form->isSubmitted() && $form->isValid()) {
 // données valides → persist
}
```

**Niveau 3 — Twig (affichage erreurs) :**
```twig
{{ form_row(form.email) }} {# Affiche le champ + label + erreur automatiquement #}
```

---

## 12. Infrastructure Docker

### 12.1 Architecture des conteneurs

```
+---------------------------------------------------------------+
|               Réseau Docker bridge : wcdo_rh_net              |
|                                                               |
|  +---------------------+        +--------------------------+  |
|  |  symfony-php        |        |  symfony-nginx           |  |
|  |  (wcdo_rh_php)      | <----- |  (wcdo_rh_nginx)         |  |
|  |  php:8.3-fpm-alpine |  9000  |  nginx:1.27-alpine       |  |
|  |  (pas de port hôte) |        |  port hôte : 8090 -> 80  |  |
|  +----------+----------+        +--------------------------+  |
|             |                                                 |
|             | pdo_mysql                                       |
|             v                                                 |
|  +---------------------+        +--------------------------+  |
|  |  mariadb            |        |  phpmyadmin              |  |
|  |  (wcdo_rh_mariadb)  | <----- |  (wcdo_rh_pma)           |  |
|  |  mariadb:10.11      |  3306  |  phpmyadmin:5            |  |
|  |  port hôte :        |        |  port hôte : 8091 -> 80  |  |
|  |  3309 -> 3306       |        |                          |  |
|  +---------------------+        +--------------------------+  |
+---------------------------------------------------------------+
```

### 12.2 Services Docker Compose

Les services sont définis dans le `docker-compose.yml` à la racine du projet. Le **nom de service** est utilisé comme hostname interne dans le réseau Docker ; le **nom de conteneur** (`container_name`) est celui visible dans `docker ps`.


**`mariadb`**
  - *Conteneur (`container_name`)* : `wcdo_rh_mariadb`
  - *Image* : `mariadb:10.11`
  - *Port hôte → conteneur* : 3309 → 3306
  - *Rôle* : Base de données

**`phpmyadmin`**
  - *Conteneur (`container_name`)* : `wcdo_rh_pma`
  - *Image* : `phpmyadmin:5`
  - *Port hôte → conteneur* : 8091 → 80
  - *Rôle* : Interface d'administration BDD

**`symfony-php`**
  - *Conteneur (`container_name`)* : `wcdo_rh_php`
  - *Image* : Build custom (`docker/symfony/Dockerfile`) — `php:8.3-fpm-alpine`
  - *Port hôte → conteneur* : — (9000 exposé uniquement dans le réseau)
  - *Rôle* : Runtime PHP-FPM Symfony

**`symfony-nginx`**
  - *Conteneur (`container_name`)* : `wcdo_rh_nginx`
  - *Image* : `nginx:1.27-alpine`
  - *Port hôte → conteneur* : **8090** → 80
  - *Rôle* : Serveur web de l'application


### 12.3 Dockerfile PHP custom

```dockerfile
FROM php:8.3-fpm-alpine
RUN apk add --no-cache git unzip icu-dev oniguruma-dev libzip-dev bash $PHPIZE_DEPS \
 && docker-php-ext-install pdo_mysql intl zip opcache \
 && apk del $PHPIZE_DEPS
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /var/www/symfony
EXPOSE 9000
CMD ["php-fpm", "-F"]
```

**Extensions PHP installées :**
- `pdo_mysql` → Doctrine / MariaDB
- `intl` → Symfony Validator (internationalisation)
- `zip` → Composer (installation des dépendances)
- `opcache` → Performance PHP

### 12.4 Configuration Nginx — Front Controller Symfony

```nginx
location / {
 try_files $uri /index.php$is_args$args;
}
location ~ ^/index\.php(/|$) {
 fastcgi_pass symfony-php:9000;
 include fastcgi_params;
 fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
}
```

**Principe du Front Controller** : toutes les requêtes passent par `public/index.php`, point d'entrée unique du Kernel Symfony. Nginx redirige tout vers ce fichier sauf les assets statiques.

### 12.5 Variables d'environnement


**`APP_ENV`**
  - *Valeur* : `dev`
  - *Source réelle* : Injectée par `docker-compose.yml` (service `symfony-php`) — surchargée en `test` lors de l'exécution de PHPUnit

**`APP_SECRET`**
  - *Valeur* : clé aléatoire
  - *Source réelle* : `.env` (ou `.env.local`, gitignored)

**`DATABASE_URL`**
  - *Valeur* : `mysql://wcdo:wcdo@mariadb:3306/wcdo_rh?serverVersion=10.11.0-MariaDB&charset=utf8mb4`
  - *Source réelle* : Injectée par `docker-compose.yml` — le hostname `mariadb` correspond au nom du service Docker, pas à `localhost`


> Remarque : la valeur de `DATABASE_URL` injectée par Docker Compose prime sur celle du fichier `.env` grâce à la directive `environment:` du service `symfony-php`.

---

## 13. Méthode de gestion de projet

### 13.1 Approche retenue

Le projet a été réalisé en **phases séquentielles** selon un pipeline défini en amont par une phase d'architecture. Cette approche garantit que chaque couche est fonctionnelle avant de passer à la suivante.

### 13.2 Pipeline de réalisation

```
Phase 1 — Infra Docker
 ↓ (stack Docker up, curl 200)
Phase 2 — Packages Composer
 ↓ (17 dépendances de production + 8 de développement installées)
Phase 3 — Entités & BDD
 ↓ (4 tables créées et vérifiées)
Phase 4 — Sécurité
 ↓ (login testé curl 302→/)
Phase 5 — Controllers & Forms
 ↓ (20 routes créées)
Phase 6 — Templates Twig
 ↓ (19 templates créés)
Phase 7 — Fixtures & Tests
 ↓ (données chargées, 10 tests verts)
```

### 13.3 Détail des phases


****1 — Infra****
  - *Contenu* : docker-compose, Dockerfile, nginx.conf, init.sql
  - *Validation* : `curl http://localhost:8090` → 200

****2 — Composer****
  - *Contenu* : Installation des dépendances (17 production + 8 dev)
  - *Validation* : `composer show` vérifié

****3 — Entités****
  - *Contenu* : 4 entités + 4 repositories, schema Doctrine
  - *Validation* : `SHOW TABLES` → 4 tables

****4 — Sécurité****
  - *Contenu* : security.yaml, SecurityController, login.html.twig
  - *Validation* : `curl POST /login` → 302 → /

****5 — Controllers****
  - *Contenu* : 6 controllers, 7 FormTypes, 20 routes
  - *Validation* : `debug:router` → 20 routes

****6 — Templates****
  - *Contenu* : 19 templates Twig Bootstrap 5
  - *Validation* : Rendu HTML vérifié

****7 — Fixtures****
  - *Contenu* : AppFixtures (8 collabs, 3 restos, 10 affectations)
  - *Validation* : Counts BDD vérifiés

****7 — Tests****
  - *Contenu* : 10 tests PHPUnit (entity + controller)
  - *Validation* : `phpunit` → 10/10 verts


### 13.4 Outils de suivi

- **Plan d'architecture** : `plan.md` (1416 lignes) — source de vérité technique
- **Orchestration** : Framework BYAN (BMAD) — agents spécialisés (Architecte → Dev → Tests → Doc)
- **Validation continue** : tests curl après chaque phase + PHPUnit pour les composants critiques

---

## 14. Tests et validation

### 14.1 Stratégie de test

Trois niveaux de tests ont été mis en place :


****Tests unitaires****
  - *Outil* : PHPUnit + `TestCase`
  - *Couverture* : Entités PHP (logique métier)

****Tests fonctionnels****
  - *Outil* : PHPUnit + `WebTestCase`
  - *Couverture* : Contrôleurs HTTP (routes, redirections)

****Tests d'intégration manuel****
  - *Outil* : curl + navigateur
  - *Couverture* : Parcours utilisateur complet


### 14.2 Tests unitaires — Entités

**`tests/Entity/CollaborateurTest.php`** (4 tests)

- **`testGetUserIdentifierReturnsEmail`** — `getUserIdentifier()` retourne l'email
- **`testDefaultRolesContainsRoleUser`** — `getRoles()` contient toujours `ROLE_USER`
- **`testSetAdministrateurTrueGrantsRoleAdmin`** — `setAdministrateur(true)` → `ROLE_ADMIN` dans `getRoles()`
- **`testSetAdministrateurFalseRemovesRoleAdmin`** — `setAdministrateur(false)` → pas de `ROLE_ADMIN`


**`tests/Entity/AffectationTest.php`** (3 tests)

- **`testIsActiveTrueWhenDateFinNull`** — `isActive()` = `true` si `dateFin = null`
- **`testIsActiveFalseWhenDateFinSet`** — `isActive()` = `false` si `dateFin` renseignée
- **`testRelationsAreSet`** — Les setters de relation fonctionnent correctement


### 14.3 Tests fonctionnels — Contrôleurs

**`tests/Controller/SecurityControllerTest.php`** (3 tests)

- **`testLoginPageRenders`** — `GET /login` → 200, formulaire présent
- **`testHomeRedirectsToLoginWhenAnonymous`** — `GET /` sans auth → redirect `/login`
- **`testLoginWithValidCredentialsRedirectsToHome`** — POST login valide → redirect `/`


**Résultat : 10 tests / 20 assertions — 100 % verts**

```
Runtime: PHP 8.3.30
OK (10 tests, 20 assertions)
Time: 00:00.173
```

### 14.4 Tests d'intégration (smoke tests curl)

Parcours complet vérifié en environnement Docker :

```
GET /              -> 302 /login   (non authentifié redirigé)
GET /login         -> 200          (formulaire affiché)
POST /login        -> 302 /        (admin@wcdo.fr / admin123)
GET /              -> 200          (dashboard affiché)
GET /restaurant    -> 200          (liste restaurants)
GET /collaborateur -> 200          (liste collaborateurs)
GET /fonction      -> 200          (liste fonctions)
GET /affectation   -> 200          (liste affectations)
```

### 14.5 Données de démonstration (Fixtures)

Chargées via `bin/console doctrine:fixtures:load -n`


**Fonctions**
  - *Quantité* : 5
  - *Détail* : Équipier polyvalent, Caissier, Manager, Préparateur, Directeur de restaurant

**Restaurants**
  - *Quantité* : 3
  - *Détail* : Wacdo Nice Centre, Wacdo Marseille Vieux-Port, Wacdo Lyon Part-Dieu

**Collaborateurs**
  - *Quantité* : 8
  - *Détail* : 2 admins (Admin Système, Dupont Marie) + 6 non-admins

**Affectations**
  - *Quantité* : 10
  - *Détail* : 7 actives + 3 terminées


**Comptes de test :**
- `admin@wcdo.fr` / `admin123` (Administrateur Système)
- `marie.dupont@wcdo.fr` / `manager1` (Dupont Marie)

---

## 15. Livrables et déploiement

### 15.1 Livrables du projet


**Code source**
  - *Localisation* : `symfony/src/`
  - *Description* : Controllers, Entities, Repositories, Forms

**Templates**
  - *Localisation* : `symfony/templates/`
  - *Description* : 19 vues Twig Bootstrap 5

**Configuration**
  - *Localisation* : `symfony/config/`
  - *Description* : security.yaml, doctrine.yaml, framework.yaml

**Infrastructure**
  - *Localisation* : `docker-compose.yml`, `docker/`
  - *Description* : Stack complète 4 services

**Tests**
  - *Localisation* : `symfony/tests/`
  - *Description* : 10 tests PHPUnit

**Fixtures**
  - *Localisation* : `symfony/src/DataFixtures/`
  - *Description* : Données de démonstration

**Documentation**
  - *Localisation* : Ce document
  - *Description* : CDCF + Dossier Technique


### 15.2 Démarrage de l'application

```bash
# Lancer la stack
cd WcdoFrameWork/
docker compose up -d

# (Première fois) Créer le schéma et charger les données
docker compose exec symfony-php bin/console doctrine:schema:create
docker compose exec symfony-php bin/console doctrine:fixtures:load -n

# Lancer les tests
docker compose exec -e APP_ENV=test symfony-php vendor/bin/phpunit tests/
```

### 15.3 URLs d'accès

- **`http://localhost:8090`** — Application WacdoRH
- **`http://localhost:8091`** — phpMyAdmin (BDD)
- **`localhost:3309`** — MariaDB (accès direct)


### 15.4 Arguments jury — Points clés à maîtriser

**Sur Symfony :**
- Symfony suit le pattern MVC et utilise le principe d'injection de dépendances (DI) via un container de services
- Le Kernel est le cœur : il charge les bundles, les configurations, et traite chaque requête via un cycle Request → Response
- Les attributs PHP 8 `#[Route]` remplacent les annotations YAML/XML des anciennes versions

**Sur Doctrine :**
- Doctrine mappe les classes PHP sur les tables SQL via les attributs `#[ORM\Entity]`, `#[ORM\Column]`
- Le QueryBuilder permet de construire des requêtes SQL dynamiques sans écrire de SQL brut
- `EntityManager` gère le cycle de vie des entités : `persist()` (planifier), `flush()` (exécuter)

**Sur la sécurité :**
- Le Firewall Symfony intercepte les requêtes avant qu'elles arrivent aux controllers
- `bcrypt` avec `cost: 13` : chaque vérification de mot de passe prend ~100ms (protection brute-force)
- `ROLE_ADMIN` dans `access_control` bloque toute la partie admin en une seule règle

**Comparaison Bloc 2 vs Bloc 3 :**
> "Dans le Bloc 2, j'ai tout codé à la main : le routeur, la gestion des sessions, la validation des formulaires, le SQL avec PDO. Dans le Bloc 3, Symfony fournit ces composants prêts à l'emploi, testés et maintenus par la communauté. Cela permet de se concentrer sur la logique métier plutôt que sur la plomberie technique."

---

*Document généré par Paige (Tech-Writer BYAN) — Avril 2026* 
*Basé sur plan.md (1416 lignes) + bloc3framework.md (sujet officiel jury)*