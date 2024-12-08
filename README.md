# Projet de Gestion de Bibliothèque

Application web développée avec Symfony 6.4 permettant de gérer une bibliothèque, ses livres, auteurs et emprunts.

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- Symfony CLI
- MySQL ou MariaDB

## Installation

1. Cloner le projet
```bash
git clone [url-du-projet]
cd [nom-du-projet]
```

2. Installer les dépendances PHP
```bash
composer install
```

3. Configurer la base de données dans le fichier `.env`
```env
DATABASE_URL="mysql://[user]:[password]@127.0.0.1:3306/[database_name]"
```

4. Créer la base de données et exécuter les migrations
```bash
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```

5. Charger les fixtures (données de test)
```bash
symfony console doctrine:fixtures:load
```

7. Démarrer le serveur Symfony
```bash
symfony server:start -d
```

### Fonctionnalités Principales

- Gestion complète des livres (CRUD)
- Gestion des auteurs (CRUD)
- Système d'authentification
- Gestion des emprunts
- Interface d'administration
- Recherche de livres

## Accès à l'Application

- URL: `http://localhost:8000`
- Compte administrateur par défaut:
    - Email: admin@example.com
    - Mot de passe: 123456

## Exemples d'Utilisation

### Création d'un Auteur

1. Via l'interface web :
    - Accédez à `/app/author/new`
    - Remplissez le formulaire avec :
      ```
      Prénom : Victor
      Nom : Hugo
      Bio : Victor Hugo est un poète, dramaturge, écrivain et homme politique français...
      ```

### Ajout d'un Livre

1. Via l'interface web :
    - Accédez à `/app/book/new`
    - Remplissez le formulaire avec :
      ```
      Titre : Les Misérables
      Genre : Roman historique
      Date de publication : 1862-04-03
      Auteur : [Sélectionnez Victor Hugo dans la liste]
      ```

### Gestion des Emprunts

1. Emprunter un livre :
    - Connectez-vous en tant qu'utilisateur
    - Accédez à la page du livre (`/app/book/{id}`)
    - Cliquez sur "Emprunter"

2. Retourner un livre :
    - Accédez à votre liste d'emprunts (`/app/borrow-list`)
    - Cliquez sur "Retourner" pour le livre concerné

### Administration des Utilisateurs

1. Modifier les rôles via l'interface :
    - Accédez à `/admin/users`
    - Utilisez le bouton "Changer le rôle" pour basculer entre ROLE_USER et ROLE_ADMIN

### Recherche de Livres

La fonction de recherche accepte :
- Les titres de livres
- Les noms d'auteurs
- Les genres

## Sécurité

- L'accès à l'administration nécessite le rôle ROLE_ADMIN
- Les routes `/app/*` nécessitent une authentification
- Protection CSRF activée sur tous les formulaires
- Mots de passe hashés avec l'algorithme par défaut de Symfony
