# BileMo

BileMo is an API REST developed for the Openclassrooms Program PHP/Symfony. It is developed with the Symfony Framework.

## Getting Started

### Prerequisites

* PHP 8.2.4
* composer
* MariaDB / MySQL

### Installation

Install the project and the dependencies:

```sh
git clone https://github.com/CelineFoucart/BileMo.git
composer install
```

Configure the database in a .env.local file. Install the starting data when the project is in dev environment:

```sh
php bin/console doctrine:database:create
php bin/console d:m:m
php bin/console doctrine:fixtures:load
```
