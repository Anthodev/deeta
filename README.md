# Symfony Api Bootstrap

A bootstrap api project for Symfony 7.0 with PHP 8.3, based on my fork of [symfony-docker](https://github.com/Anthodev/symfony-docker) (using Frankenphp) from [Kévin Dunglas](https://dunglas.dev).

![GitHub license](https://img.shields.io/github/license/anthodev/symfony-api-bootstrap) ![CI](https://github.com/anthodev/symfony-api-bootstrap/workflows/CI/badge.svg) ![GitHub issues](https://img.shields.io/github/issues/anthodev/symfony-api-bootstrap)

## Features

* PHP 8.3 & Symfony 7.0
* Confirmation email system
* The security system is already configured
* User and role entities are ready
* A Voter system is already in place
* Tests are present and ready to be run
* The CI is already configured and working

## Routes available
```
GET     /api/ping
POST    /api/register                   (payload: email, username, plainPassword)
GET     /api/register/confirm/{token}
GET     /api/auth_ping                  (protected route)
GET     /api/users                      (protected route)
GET     /api/users/{id}                 (protected route)
PATCH   /api/users/{id}                 (protected route, payload: email, username, plainPassword)
DELETE  /api/users/{id}                 (protected route)
```

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to start the project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

### Taskfile
A `Taskfile` is included to help you with common tasks. Run `task` to see the available commands.

Task commands:
```
* bash:                   Open a bash session                              (aliases: b)
* build-dev:              Build the development environment                (aliases: bd)
* composer-install:       Install the composer dependencies                (aliases: c)
* composer-require:       Require a composer package                       (aliases: cr)
* create-db:              Create the database                              (aliases: cdb)
* create-db-test:         Create the test database                         (aliases: cdbt)
* drop-db:                Drop the database                                (aliases: ddb)
* drop-db-test:           Drop the test database                           (aliases: ddbt)
* ecs:                    Run the code style fixer                         (aliases: e)
* migrate:                Run the migrations                               (aliases: m)
* migrate-test:           Run the migrations for the test environment      (aliases: mt)
* prune:                  Remove all stopped containers                    (aliases: p)
* stan:                   Run the static analysis                          (aliases: st)
* stop:                   Stop the containers                              (aliases: s)
* tests:                  Run the tests                                    (aliases: t)
* up:                     Start the containers                             (aliases: u)
* up-db:                  Start the database container                     (aliases: udb)
```

## License

Symfony Api Bootstrap is available under the MIT License.

## Credits

Created by [Cedric Anthony](https://antho.dev), based on my fork of [symfony-docker](https://github.com/Anthodev/symfony-docker) from [Kévin Dunglas](https://dunglas.dev).
