# BileMoAPI

[![SymfonyInsight](https://insight.symfony.com/projects/5237d936-c1e7-4301-ab67-c3e090224d7b/mini.svg)](https://insight.symfony.com/projects/5237d936-c1e7-4301-ab67-c3e090224d7b)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/93ef707ab6624d9d8375efae5337d570)](https://www.codacy.com/gh/kdefarge/BileMoAPI/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=kdefarge/BileMoAPI&amp;utm_campaign=Badge_Grade)

BileMoAPI is a Symfony API application with [api-platform](https://github.com/api-platform/api-platform)

## Table of Contents

  - [Installation](#Installation)
  - [Setup](#Setup)
  - [Fixture](#Fixture)
  - [Maintainers](#Maintainers)

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
# executes all migration files
php bin/console doctrine:migrations:migrate
```

## Fixture

### Run dev fixture

```bash
# load all the 'dev' fixtures
php bin/console hautelook:fixtures:load
```

## Maintainers

[@kdefarge](https://github.com/kdefarge)

## License

[MIT](LICENSE) © Kévin DEFARGE
