# Gymly

Gymly is a deliberately overengineered showcase project in the fitness domain. Its purpose is to demonstrate, in a compact yet cohesive system, how to design and implement a gym training-plans service: creating training plans, defining required exercises and minimum sets, and managing the plan lifecycle. At the same time, the codebase intentionally applies a range of engineering techniques to illustrate their practical use.

In this project you will find, among others:
- patterns and approaches: CQRS, Event Sourcing, DDD elements (aggregates, domain events), layered/hexagonal architecture,
- interfaces: REST API (API Platform, OpenAPI, Swagger),
- infrastructure: Docker, RoadRunner (worker pool, event-driven),
- quality: unit and behavioral tests, static analysis, architectural rules,
- tools: PHP 8.4, Symfony 7, API Platform, php-cs-fixer, PHPStan, PHPUnit, Behat, Rector, phparkitect, RoadRunner.

## How to run
Requirements: Docker + Docker Compose.

1) Start the containers in the background:

```bash
docker compose up -d
```

2) Enter the application container and install dependencies:

```bash
docker compose exec php sh
composer install
```

After startup Swagger (OpenAPI UI) is available at:

- http://localhost:8080/api/docs

## Business rules
At a high level, here is what you can do in the system:

- Training plans
  - Create a training plan with a name and a list of required exercises (each with a minimum number of sets).
  - Add new exercises that aren’t already in the plan, or remove exercises that are no longer needed.
  - Once a plan is deleted, it becomes inactive and can’t be modified.

- Workout sessions
  - Start a workout either with a selected training plan or without one.
  - During a workout, record sets for exercises; when a plan is selected, stick to the exercises required by that plan.
  - Finish a workout after you’ve done at least one set and, if you started with requirements, you’ve met them.
  - You can also cancel a workout that’s in progress.

## Useful scripts
The `composer.json` file contains handy aliases for running tools. Below are a few convenient examples. Run the above commands inside the `php` container (e.g., after `docker compose exec php sh`).

- Static analysis and code quality:

```bash
composer run cs:check # Style check (php-cs-fixer - dry-run)
composer run phpstan # Static analysis (PHPStan)
composer run rector:dry # Rector (dry-run)
composer run phparkitect # Architectural rules (phparkitect)

composer run static-analysis # Full static analysis suite
composer run sa # Short for the command above

# Apply changes to code (php-cs-fixer, rector)
composer run cs:fix
composer run rector:process
```

- Tests:

```bash
composer run tests:unit # Unit tests
composer run tests:behat # Behavioral tests
composer run tests # Both of the above
```

- Combined quality (analysis + tests):

```bash
composer run quality-assurance
composer run qa # Short for the command above
```
