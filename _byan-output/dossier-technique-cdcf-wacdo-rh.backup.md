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

| Bloc | Technologie | Périmètre |
|------|-------------|-----------|
| Bloc 1 | HTML / CSS / JavaScript natif | Interface borne de commande |
| Bloc 2 | PHP natif MVC + PDO + API REST | Back-office commandes |
| **Bloc 3** | **Symfony 7 + Doctrine + Twig** | **Application RH (ce projet)** |

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

| Acteur | Description | Droits |
|--------|-------------|--------|
| **Administrateur** | Collaborateur Wacdo avec le flag `administrateur = true` et un mot de passe | Accès complet à toutes les fonctionnalités |
| **Collaborateur standard** | Collaborateur sans droit admin | Aucun accès à l'application |
| **Visiteur anonyme** | Non authentifié | Redirigé vers la page de connexion |

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

| ID | Besoin | Description |
|----|--------|-------------|
| BNF-01 | Sécurité | Authentification robuste, mots de passe hachés, protection CSRF |
| BNF-02 | Maintenabilité | Code structuré selon les conventions Symfony (MVC, DI, conventions de nommage) |
| BNF-03 | Portabilité | Application conteneurisée via Docker, déployable sur tout environnement |
| BNF-04 | Testabilité | Tests unitaires et fonctionnels couvrant les composants critiques |
| BNF-05 | Ergonomie | Interface responsive Bootstrap 5, navigation claire |

---

## 4. Contraintes

### 4.1 Contraintes techniques (imposées par le sujet d'examen)

| Contrainte | Détail |
|------------|--------|
| **Framework back** | Utilisation obligatoire d'un framework PHP côté serveur |
| **ORM** | Gestion des entités via un ORM (Doctrine) |
| **Moteur de templates** | Utilisation obligatoire d'un moteur de templates (Twig) |
| **Base de données SQL** | MySQL / MariaDB |
| **Sécurité** | Authentification et autorisation des utilisateurs obligatoires |
| **Tests** | Tests d'interface, fonctionnels et de sécurité requis |

### 4.2 Contraintes projet

| Contrainte | Détail |
|------------|--------|
| **Isolement** | L'application Bloc 3 est 100% indépendante des Blocs 1 et 2 |
| **Base de données séparée** | Base `wcdo_rh` dédiée, distincte de la base `wcdo` des Blocs 1/2 |
| **Environnement conteneurisé** | Docker obligatoire pour la portabilité |
| **PHP 8.3** | Version moderne du langage |

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

| Critère | Symfony 7 | Laravel | PHP natif (Bloc 2) |
|---------|-----------|---------|---------------------|
| Popularité en France | Leader (3/3) | Fort (2/3) | N/A |
| Courbe d'apprentissage | Moyenne | Douce | Déjà maîtrisé |
| Composants intégrés | Tous (Security, Form, Validator…) | Équivalents | À coder soi-même |
| ORM natif | Doctrine | Eloquent | PDO manuel |
| Conventions strictes | Oui (+ pédagogique pour jury) | Moins | N/A |
| LTS / Stabilité | Oui (Symfony 7.4 LTS) | Oui | N/A |
| Adéquation examen | **Référence en formation FR** | Bon | Insuffisant pour le bloc |

**Symfony** a été retenu car c'est le framework de référence en France pour la formation développeur web, avec un écosystème de composants riche et des conventions claires qui démontrent la maîtrise d'un vrai framework professionnel.

### 5.3 Ce que le framework apporte vs PHP natif (Bloc 2)

| Bloc 2 — PHP Natif | Bloc 3 — Symfony |
|--------------------|-----------------|
| `Router.php` custom (200 lignes) | Composant Routing (attributs PHP 8) |
| `$_POST` lu manuellement | FormType avec binding automatique |
| `password_hash()` manuel | `UserPasswordHasherInterface` |
| `$_SESSION` géré à la main | Firewall Symfony + Security Component |
| SQL PDO `$stmt->prepare()` | Doctrine QueryBuilder + EntityManager |
| HTML dans des fichiers `.php` | Templates Twig avec héritage |
| Validation manuelle de chaque champ | Attributs Assert sur les entités |

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

| Critère | Symfony 7 | Laravel 11 | CodeIgniter 4 | Slim 4 |
|---------|-----------|------------|---------------|--------|
| Type | Full-stack | Full-stack | Full-stack | Micro |
| ORM intégré | Doctrine (externe mais standard) | Eloquent (intégré) | Léger (intégré) | Aucun |
| Sécurité | Composant dédié (3/3) | Guards (3/3) | Basique (2/3) | Manuel (1/3) |
| Formulaires | FormType complet (3/3) | Form Request (2/3) | Basique (2/3) | Aucun composant natif |
| Twig natif | Oui | Non (Blade) | Non | Non |
| Popularité France | 1er | 2e | 3e | Niche |
| Usage formation FR | Référence | Répandu | Minoritaire | Rare |

**Choix retenu : Symfony 7.4** — Standard de l'industrie en France, composants matures, idéal pour démontrer la maîtrise d'un framework complet.

### 7.2 ORM

| Critère | Doctrine ORM | Eloquent | PDO natif (Bloc 2) |
|---------|-------------|----------|---------------------|
| Mapping objet-relationnel | Complet | Complet | Manuel |
| Migrations | Automatiques | Automatiques | Manuel |
| QueryBuilder | Puissant | Fluent | N/A |
| Relations | OneToMany / ManyToOne / ManyToMany | Idem | Requêtes manuelles |
| Intégration Symfony | Native | Addon | N/A |

**Choix retenu : Doctrine ORM 3** — Standard de facto avec Symfony, attributs PHP 8 modernes.

### 7.3 Moteur de templates

| Critère | Twig 3 | Blade (Laravel) | PHP Natif |
|---------|--------|-----------------|-----------|
| Héritage de templates | Oui (`extends`/`block`) | Oui | Non (include seulement) |
| Syntaxe sécurisée (auto-escape) | Oui, par défaut | Oui | Non (à faire manuellement) |
| Intégration formulaires Symfony | Native | N/A | N/A |
| Lisibilité | (3/3) | (3/3) | (2/3) |
| Form themes Bootstrap 5 | Intégré | N/A | N/A |

**Choix retenu : Twig 3** — Natif Symfony, héritage de templates (`{% extends %}`), auto-escape XSS, thème Bootstrap 5 prêt à l'emploi.

### 7.4 Base de données

| Critère | MariaDB 10.11 | PostgreSQL 16 | SQLite 3 |
|---------|---------------|---------------|----------|
| Compatibilité dialecte MySQL | Oui (fork quasi drop-in) | Non (dialecte PostgreSQL distinct) | Partielle (SQL ANSI simplifié) |
| Production ready | Oui | Oui | Oui (embarqué : mobile, Firefox, iOS, etc.) mais single-writer, peu adapté à un back-office concurrent |
| Déjà en place sur l'infra Blocs 1/2 | Oui | Non | N/A |
| Performance (lecture/écriture concurrente) | (3/3) | (3/3) | (2/3) |
| Image Docker officielle | Oui (`mariadb`) | Oui (`postgres`) | N/A (librairie embarquée) |
| Support Doctrine DBAL | Oui (driver `pdo_mysql`) | Oui (driver `pdo_pgsql`) | Oui (driver `pdo_sqlite`) |

**Choix retenu : MariaDB 10.11** — Déjà présente dans l'infrastructure Docker de la formation, compatible avec le dialecte MySQL imposé par le sujet, et adaptée aux accès concurrents d'une application multi-utilisateurs. Création d'une nouvelle base dédiée `wcdo_rh` isolée des bases `wcdo` des Blocs 1/2.

### 7.5 CSS / Frontend

| Critère | Bootstrap 5 (CDN) | Tailwind CSS | CSS Custom |
|---------|-------------------|--------------|------------|
| Rapidité d'intégration | (3/3) | (2/3) | (1/3) |
| Composants prêts (tables, forms, badges) | Riche bibliothèque | Partiel (Tailwind UI en option payante) | À écrire manuellement |
| Form theme Symfony natif | `bootstrap_5_layout.html.twig` fourni | Non natif | N/A |
| Temps de développement | Très court | Court | Long |
| Pertinence back-office | Bonne (composants admin classiques) | Bonne (mais nécessite build Tailwind) | Surqualité pour un back-office |

**Choix retenu : Bootstrap 5 via CDN** — Back-office propre et responsive sans perdre de temps sur le CSS. Le focus du Bloc 3 est sur PHP/Symfony, pas sur le design.

### 7.6 Récapitulatif des dépendances Composer

> Contraintes de la plateforme (`composer.json`) : `php: >=8.2`, `ext-ctype: *`, `ext-iconv: *`.  
> Runtime réellement utilisé dans le conteneur : **PHP 8.3.30** (image `php:8.3-fpm-alpine`).

#### Dépendances de production (`require`)

| Package | Version | Rôle |
|---------|---------|------|
| `doctrine/doctrine-bundle` | ^2.18 | Intégration Doctrine dans Symfony |
| `doctrine/doctrine-migrations-bundle` | ^3.7 | Migrations de schéma |
| `doctrine/orm` | ^3.6 | ORM Doctrine (mapping, EntityManager) |
| `symfony/asset` | 7.4.* | Helper `asset()` dans Twig |
| `symfony/console` | 7.4.* | Commandes `bin/console` |
| `symfony/dotenv` | 7.4.* | Chargement des fichiers `.env` |
| `symfony/flex` | ^2 | Installateur de recipes Symfony |
| `symfony/form` | 7.4.* | FormType, rendu, validation |
| `symfony/framework-bundle` | 7.4.* | Bundle de base du framework |
| `symfony/runtime` | 7.4.* | Runtime découplé (FPM / CLI) |
| `symfony/security-bundle` | 7.4.* | Firewall, providers, hashers, rôles |
| `symfony/security-csrf` | 7.4.* | Protection CSRF des formulaires |
| `symfony/twig-bundle` | 7.4.* | Intégration Twig + thème Bootstrap 5 |
| `symfony/validator` | 7.4.* | Contraintes `Assert` sur les entités |
| `symfony/yaml` | 7.4.* | Lecture des fichiers de configuration YAML |
| `twig/extra-bundle` | ^2.12 \| ^3.0 | Extensions Twig supplémentaires |
| `twig/twig` | ^2.12 \| ^3.0 | Moteur de templates Twig |

#### Dépendances de développement (`require-dev`)

| Package | Version | Rôle |
|---------|---------|------|
| `doctrine/doctrine-fixtures-bundle` | ^4.3 | Chargement des fixtures (données de démo) |
| `phpunit/phpunit` | ^12.5 | Framework de tests unitaires et fonctionnels |
| `symfony/browser-kit` | 7.4.* | Client HTTP pour `WebTestCase` |
| `symfony/css-selector` | 7.4.* | Sélecteurs CSS pour les assertions `Crawler` |
| `symfony/debug-bundle` | 7.4.* | Affichage amélioré des erreurs (dev) |
| `symfony/maker-bundle` | ^1.67 | CLI `make:entity`, `make:controller`, … |
| `symfony/stopwatch` | 7.4.* | Mesure de performance pour le profiler |
| `symfony/web-profiler-bundle` | 7.4.* | Web Debug Toolbar et profiler (dev) |

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

| Repository | Méthode | Description |
|-----------|---------|-------------|
| `CollaborateurRepository` | `findByFilters(?nom, ?prenom, ?email)` | LIKE conditionnel sur chaque champ |
| `CollaborateurRepository` | `findNonAffectes()` | LEFT JOIN affectations WITH `dateFin IS NULL`, WHERE affectation.id IS NULL → retourne les collaborateurs sans aucune affectation active |
| `RestaurantRepository` | `findByFilters(?nom, ?cp, ?ville)` | LIKE conditionnel |
| `AffectationRepository` | `findByFilters(?fonction, ?dateDebut, ?dateFin, ?ville)` | JOIN restaurant + conditions |
| `AffectationRepository` | `findActivesByRestaurant($restaurant, ...)` | WHERE date_fin IS NULL + filtres |
| `AffectationRepository` | `findAllByRestaurant($restaurant, ...)` | Toutes affectations d'un restaurant |
| `AffectationRepository` | `findActivesByCollaborateur($collab)` | Affectations actives d'un collaborateur |
| `AffectationRepository` | `findAllByCollaborateur($collab, ...)` | Historique complet d'un collaborateur |

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

| # | Route | URL | Méthodes | Controller |
|---|-------|-----|----------|------------|
| 1 | `app_login` | `/login` | GET, POST | SecurityController |
| 2 | `app_logout` | `/logout` | GET | SecurityController |
| 3 | `app_dashboard` | `/` | ANY | DashboardController |
| 4 | `app_restaurant_index` | `/restaurant` | GET | RestaurantController |
| 5 | `app_restaurant_new` | `/restaurant/new` | GET, POST | RestaurantController |
| 6 | `app_restaurant_show` | `/restaurant/{id}` | GET | RestaurantController |
| 7 | `app_restaurant_edit` | `/restaurant/{id}/edit` | GET, POST | RestaurantController |
| 8 | `app_restaurant_affecter` | `/restaurant/{id}/affecter` | GET, POST | RestaurantController |
| 9 | `app_collaborateur_index` | `/collaborateur` | GET | CollaborateurController |
| 10 | `app_collaborateur_non_affectes` | `/collaborateur/non-affectes` | GET | CollaborateurController |
| 11 | `app_collaborateur_new` | `/collaborateur/new` | GET, POST | CollaborateurController |
| 12 | `app_collaborateur_show` | `/collaborateur/{id}` | GET | CollaborateurController |
| 13 | `app_collaborateur_edit` | `/collaborateur/{id}/edit` | GET, POST | CollaborateurController |
| 14 | `app_collaborateur_affecter` | `/collaborateur/{id}/affecter` | GET, POST | CollaborateurController |
| 15 | `app_fonction_index` | `/fonction` | GET | FonctionController |
| 16 | `app_fonction_new` | `/fonction/new` | GET, POST | FonctionController |
| 17 | `app_fonction_edit` | `/fonction/{id}/edit` | GET, POST | FonctionController |
| 18 | `app_affectation_index` | `/affectation` | GET | AffectationController |
| 19 | `app_affectation_new` | `/affectation/new` | GET, POST | AffectationController |
| 20 | `app_affectation_edit` | `/affectation/{id}/edit` | GET, POST | AffectationController |

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

| Aspect | Bloc 2 (PHP natif) | Bloc 3 (Symfony) |
|--------|-------------------|-----------------|
| Hash mdp | `password_hash(PASSWORD_BCRYPT)` | `UserPasswordHasherInterface::hashPassword()` |
| Vérification | `password_verify()` manuel | Automatique via Firewall |
| Session | `$_SESSION['user']` manuel | Token Symfony Security |
| CSRF | Généré/vérifié manuellement | Automatique sur tous FormTypes |
| Accès restreint | `if(!$_SESSION['admin']) redirect()` | `access_control` dans security.yaml |

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

| Service (hostname interne) | Conteneur (`container_name`) | Image | Port hôte → conteneur | Rôle |
|---|---|---|---|---|
| `mariadb` | `wcdo_rh_mariadb` | `mariadb:10.11` | 3309 → 3306 | Base de données |
| `phpmyadmin` | `wcdo_rh_pma` | `phpmyadmin:5` | 8091 → 80 | Interface d'administration BDD |
| `symfony-php` | `wcdo_rh_php` | Build custom (`docker/symfony/Dockerfile`) — `php:8.3-fpm-alpine` | — (9000 exposé uniquement dans le réseau) | Runtime PHP-FPM Symfony |
| `symfony-nginx` | `wcdo_rh_nginx` | `nginx:1.27-alpine` | **8090** → 80 | Serveur web de l'application |

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

| Variable | Valeur | Source réelle |
|----------|--------|---------------|
| `APP_ENV` | `dev` | Injectée par `docker-compose.yml` (service `symfony-php`) — surchargée en `test` lors de l'exécution de PHPUnit |
| `APP_SECRET` | clé aléatoire | `.env` (ou `.env.local`, gitignored) |
| `DATABASE_URL` | `mysql://wcdo:wcdo@mariadb:3306/wcdo_rh?serverVersion=10.11.0-MariaDB&charset=utf8mb4` | Injectée par `docker-compose.yml` — le hostname `mariadb` correspond au nom du service Docker, pas à `localhost` |

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

| Phase | Contenu | Validation |
|-------|---------|------------|
| **1 — Infra** | docker-compose, Dockerfile, nginx.conf, init.sql | `curl http://localhost:8090` → 200 |
| **2 — Composer** | Installation des dépendances (17 production + 8 dev) | `composer show` vérifié |
| **3 — Entités** | 4 entités + 4 repositories, schema Doctrine | `SHOW TABLES` → 4 tables |
| **4 — Sécurité** | security.yaml, SecurityController, login.html.twig | `curl POST /login` → 302 → / |
| **5 — Controllers** | 6 controllers, 7 FormTypes, 20 routes | `debug:router` → 20 routes |
| **6 — Templates** | 19 templates Twig Bootstrap 5 | Rendu HTML vérifié |
| **7 — Fixtures** | AppFixtures (8 collabs, 3 restos, 10 affectations) | Counts BDD vérifiés |
| **7 — Tests** | 10 tests PHPUnit (entity + controller) | `phpunit` → 10/10 verts |

### 13.4 Outils de suivi

- **Plan d'architecture** : `plan.md` (1416 lignes) — source de vérité technique
- **Orchestration** : Framework BYAN (BMAD) — agents spécialisés (Architecte → Dev → Tests → Doc)
- **Validation continue** : tests curl après chaque phase + PHPUnit pour les composants critiques

---

## 14. Tests et validation

### 14.1 Stratégie de test

Trois niveaux de tests ont été mis en place :

| Niveau | Outil | Couverture |
|--------|-------|------------|
| **Tests unitaires** | PHPUnit + `TestCase` | Entités PHP (logique métier) |
| **Tests fonctionnels** | PHPUnit + `WebTestCase` | Contrôleurs HTTP (routes, redirections) |
| **Tests d'intégration manuel** | curl + navigateur | Parcours utilisateur complet |

### 14.2 Tests unitaires — Entités

**`tests/Entity/CollaborateurTest.php`** (4 tests)

| Test | Assertion |
|------|-----------|
| `testGetUserIdentifierReturnsEmail` | `getUserIdentifier()` retourne l'email |
| `testDefaultRolesContainsRoleUser` | `getRoles()` contient toujours `ROLE_USER` |
| `testSetAdministrateurTrueGrantsRoleAdmin` | `setAdministrateur(true)` → `ROLE_ADMIN` dans `getRoles()` |
| `testSetAdministrateurFalseRemovesRoleAdmin` | `setAdministrateur(false)` → pas de `ROLE_ADMIN` |

**`tests/Entity/AffectationTest.php`** (3 tests)

| Test | Assertion |
|------|-----------|
| `testIsActiveTrueWhenDateFinNull` | `isActive()` = `true` si `dateFin = null` |
| `testIsActiveFalseWhenDateFinSet` | `isActive()` = `false` si `dateFin` renseignée |
| `testRelationsAreSet` | Les setters de relation fonctionnent correctement |

### 14.3 Tests fonctionnels — Contrôleurs

**`tests/Controller/SecurityControllerTest.php`** (3 tests)

| Test | Assertion |
|------|-----------|
| `testLoginPageRenders` | `GET /login` → 200, formulaire présent |
| `testHomeRedirectsToLoginWhenAnonymous` | `GET /` sans auth → redirect `/login` |
| `testLoginWithValidCredentialsRedirectsToHome` | POST login valide → redirect `/` |

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

| Entité | Quantité | Détail |
|--------|----------|--------|
| Fonctions | 5 | Équipier polyvalent, Caissier, Manager, Préparateur, Directeur de restaurant |
| Restaurants | 3 | Wacdo Nice Centre, Wacdo Marseille Vieux-Port, Wacdo Lyon Part-Dieu |
| Collaborateurs | 8 | 2 admins (Admin Système, Dupont Marie) + 6 non-admins |
| Affectations | 10 | 7 actives + 3 terminées |

**Comptes de test :**
- `admin@wcdo.fr` / `admin123` (Administrateur Système)
- `marie.dupont@wcdo.fr` / `manager1` (Dupont Marie)

---

## 15. Livrables et déploiement

### 15.1 Livrables du projet

| Livrable | Localisation | Description |
|----------|-------------|-------------|
| Code source | `symfony/src/` | Controllers, Entities, Repositories, Forms |
| Templates | `symfony/templates/` | 19 vues Twig Bootstrap 5 |
| Configuration | `symfony/config/` | security.yaml, doctrine.yaml, framework.yaml |
| Infrastructure | `docker-compose.yml`, `docker/` | Stack complète 4 services |
| Tests | `symfony/tests/` | 10 tests PHPUnit |
| Fixtures | `symfony/src/DataFixtures/` | Données de démonstration |
| Documentation | Ce document | CDCF + Dossier Technique |

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

| URL | Service |
|-----|---------|
| `http://localhost:8090` | Application WacdoRH |
| `http://localhost:8091` | phpMyAdmin (BDD) |
| `localhost:3309` | MariaDB (accès direct) |

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
