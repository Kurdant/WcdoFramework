# Wacdo RH — Bloc 3 Symfony 7

Application de gestion RH (examen RNCP Niveau 5, Bloc 3).

## Stack
- Symfony 7.4 · Doctrine ORM 3 · Twig · MariaDB 10.11 · PHP 8.3 · Nginx · Docker

## Démarrage rapide

```bash
docker compose up -d --build
# première fois : installer les dépendances
docker compose exec symfony-php composer install
# créer la BDD + schéma + fixtures
docker compose exec symfony-php bin/console doctrine:migrations:migrate -n
docker compose exec symfony-php bin/console doctrine:fixtures:load -n
```

### Mise en ligne rapide

Le projet est prévu pour tourner avec Docker. L'application Symfony est servie par Nginx sur le port `8090`.

Variables utiles avant lancement :

```bash
export APP_SECRET='change-me-on-a-real-server'
docker compose up -d --build
```

## Accès
- App RH : http://localhost:8090
- App RH (LAN) : http://<ip-du-serveur>:8090
- phpMyAdmin : http://localhost:8091 (root / root) — exposé uniquement en local
- Login admin : `admin@wcdo.fr` / `admin123`

## Structure
```
WcdoFrameWork/
├── symfony/               ← app Symfony 7.4
├── docker/
│   ├── symfony/           ← Dockerfile PHP-FPM + nginx.conf
│   └── mariadb/init.sql   ← création BDD wcdo_rh
├── docker-compose.yml     ← 4 services (mariadb, phpmyadmin, php, nginx)
├── plan.md                ← architecture complète
├── contexte_bloc3.md      ← contexte détaillé
└── bloc3framework.md      ← sujet officiel jury
```
# WcdoFramework
# WcdoFramework
