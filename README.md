# BileMoAPI

[![SymfonyInsight](https://insight.symfony.com/projects/5237d936-c1e7-4301-ab67-c3e090224d7b/mini.svg)](https://insight.symfony.com/projects/5237d936-c1e7-4301-ab67-c3e090224d7b)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/93ef707ab6624d9d8375efae5337d570)](https://www.codacy.com/gh/kdefarge/BileMoAPI/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=kdefarge/BileMoAPI&amp;utm_campaign=Badge_Grade)

BileMoAPI is a Symfony API application with [api-platform](https://github.com/api-platform/api-platform)

## Table of Contents

-   [Installation](#Installation)
-   [Setup](#Setup)
-   [Running BileMo API](#Running-BileMo-API)
-   [Fixture](#Fixture)
-   [Test](#Test)
-   [Maintainers](#Maintainers)

## Installation

### install BileMoAPI with composer

```bash
git clone git@github.com:kdefarge/BileMoAPI.git
cd BileMoAPI
composer install
```

## Setup

### Update .env file

```bash
# Config database
# MariaDB (dont forget version X.X.X with your version)
DATABASE_URL="mysql://USER:PASSWRD@SERVER:PORT/DB_NAME?serverVersion=mariadb-X.X.X"
```

### Install database

```bash
# Doctrine can create the DB_NAME database for you
php bin/console doctrine:database:create
# Create database schema
php bin/console doctrine:schema:create
```

### Generate the SSL keys

You need OpenSLL and run this command to generate the SSL keys

```bash
php bin/console lexik:jwt:generate-keypair
```

Update jwt config in your .env

```bash
###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=b11c0ae29c7f88b75d5e71281f38ac69
###< lexik/jwt-authentication-bundle ###
```

## Running BileMo API

```bash
cd BileMoAPI
symfony server:start
```

Open your browser and navigate to http://localhost:8000/. If everything is working, you’ll see a welcome page. Later, when you are finished working, stop the server by pressing Ctrl+C from your terminal.

To access the API documentation navigate to https://localhost:8000/docs

## Fixture

### Run dev fixture

```bash
# load all the 'dev' fixtures
php bin/console hautelook:fixtures:load
```

## Test

### Select your PHPUnit version

```xml
<!-- phpunit.xml.dist -->
<server name="SYMFONY_PHPUNIT_VERSION" value="9.5.4" />
```

### Run all test

```bash
php bin/phpunit
```

### Run one part test

```bash
php bin/phpunit --filter testLoginJWT
php bin/phpunit --filter testMobile
php bin/phpunit --filter testCustumer
php bin/phpunit --filter testUsers
php bin/phpunit --filter testCommands
```

### Test other database

Update .env.test and DATABASE_URL then create the database with schema

```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:create --env=test
```

## Tools used

-   [Symfony](https://github.com/symfony/symfony)
-   [API Platform](https://github.com/api-platform/api-platform)
-   [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)
-   [PHPUnit](https://github.com/sebastianbergmann/phpunit)
-   [AliceBundle](https://github.com/hautelook/AliceBundle)

## Maintainers

[@kdefarge](https://github.com/kdefarge)

## License

[MIT](https://github.com/kdefarge/BileMoAPI/blob/master/LICENSE.md) © Kévin DEFARGE
