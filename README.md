## Getting started

```bash
docker-compose up -d
```

```
# URL
http://127.0.0.1:8000
```

## Commandes utiles
```
# Lister l'ensemble des commandes existances 
php bin/console

# Supprimer le cache du navigateur
php bin/console cache:clear

# Création de fichier vierge
php bin/console make:controller
php bin/console make:form

# Crétion d'un CRUD complet
php bin/console make:crud

# HASH PASSWORD

php bin/console security:hash-password   
```

## Gestion de base de données

#### Commandes de création d'entité
```
php bin/console make:entity
```
Document sur les relations entre les entités
https://symfony.com/doc/current/doctrine/associations.html

#### Mise à jour de la base de données
```
# Voir les requètes qui seront jouer avec force
php bin/console doctrine:schema:update --dump-sql

# Executer les requètes en DB
php bin/console doctrine:schema:update --force

# Executer migration
php bin/console doctrine:migrations:migrate
```

## Fixtures
```
php bin/console doctrine:fixtures:load
```

## Voters
```
php bin/console make:voter
```

## cache clear
```
php bin/console cache:clear
```
