# Nuukotchi
> Projet de base de données avancée dans le cadre de LiveCampus.

![php-version]


Ce projet est un gestionnaire de tamagotchi. Vous pourrez vous authentifier afin d'accéder à vos tamagotchis ainsi que d'en créer des nouveaux. Une fois cela, il sera possible d'interagir avec chacun d'entre eux pour les nourrir, jouer avec, les coucher ou encore les faire boire afin d'accroitre leur niveau (mais attention à ne pas les tuer !).

## Prérequis
* PHP 8.1
* Symfony
* Composer
* Base de données SQL

## Installation

```bash
# Clone le répertoire
git clone https://github.com/LiveCampus/Nuuk-Advanced-DB

# Va dans le dossier du projet
cd Nuuk-Advanced-DB

# Installe les dépendances
composer install # /!\ Attention à ce que composer soit avec un PHP 8.1.*
```

## Utilisation

Créer un fichier `.env.local` à la racine du projet et coller cette ligne en changeant les informations nécessaire (host, utilisateur, mot de passe, port, version)
```yaml
DATABASE_URL="[mysql]://[user]:[password]@127.0.0.1:[3306]/nuukotchi?serverVersion=[8.0]&charset=utf8"
```

Ensuite exécuter ces deux commandes (toujours à la racine du projet)
```bash
# Créer la base de données
symfony console d:d:c

# Exécute les migrations
symfony console d:m:m
```

Vous pouvez maintenant démarrer le serveur et utiliser l'application !
```bash
symfony server:start -d
```

## Auteurs

* Hugo BOLLAERT
* Yannis BONNE
* Maxime BLACHÈRE
* Théo POSTY

[php-version]: https://img.shields.io/packagist/dependency-v/symfony/symfony/php