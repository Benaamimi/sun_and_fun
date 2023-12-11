# Sun&Fun

Sun and Fun est un site internet présentant des réservations de chambres d'hôtes

## Environnement de développement

### Pré-requis

* PHP 8.2
* Composer
* Symfony CLI

Vous pouvez vérifier les prérequis avec la commande suivante :

```bash
symfony check:requirements
```

### lancer l'environnement de développement

```bash
symfony new nom_du_projet --version="6.3.*"
composer require webapp
```

### Création de filtre twig

```bash
 symfony console make:twig-extension
 composer require liip/imagine-bundle
```

### Envoie de mail

```bash
 composer require symfony/mailer
 composer require symfony/brevo-mailer
```