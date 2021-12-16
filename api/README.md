# Habits RESTful API

This web API is built with [Laravel](https://laravel.com/docs/8.x), consult its documentation for more information.

## Prerequisites

-   php 8.0+
-   MySQL
-   composer

## Development

### Setting up

-   Make a copy of the environment variables

    ```bash
    cp .env.example .env
    ```

    Then add your database credentials

-   Install the dependencies

    ```bash
    composer install
    ```

-   Serve the API with the following command:
    ```bash
    php artisan serve
    ```

-   Run the scheduler with the following command:
    ```bash
    php artisan schedule:work
    ```

### Test Suite

For the test suite, [PHPSpec](http://phpspec.net/en/stable/manual/introduction.html) is used for the unit tests, and [Pest](https://pestphp.com) (which is a wrapper over PHPUnit) is used for the integration tests. Consult their appropriate documentations for more information.

-   Run all the tests with:

    ```bash
    composer test
    ```

-   Run the unit tests with:

    ```bash
    composer test:unit
    ```

-   Run the integration tests with
    ```bash
    composer test:integration
    ```

### Coding Standard Fixer

[PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer#php-coding-standards-fixer) is used in order to maintain a consistent coding standard throughout the PHP codebase. The configuration file is located in the root as `.php-cs-fixer.php`. Consult its documentation for more information.

-   To check the coding standard of the php codebase, run:

    ```bash
    composer sniff
    ```

-   To check the coding standard automatically fix the coding standard of the php codebase, run:

    ```bash
    composer lint
    ```
