This is an API implementation for the Book Library system using Laravel

Open the terminal and clone the project.
After cloning, cd into the directory where the project is cloned and do these steps:

#### Setup

1. In your ``/etc/hosts`` file you should add ``127.0.0.1 book-library.devel``
2. ``git checkout main`` - if you are not on the branch
3. ``cp ./.env.example ./.env``
4. ``docker compose build --no-cache``
5. ``cp src/.env.example src/.env``
6. ``docker compose up -d nginx && docker compose up -d phpmyadmin``
7. ``docker compose run --rm composer install``
8. ``docker compose run --rm artisan migrate``
-----------------------------------------------------------------------------------------------

#### Seeders

PhpMyAdmin will be available on ``http://book-library.devel:8090/`` or ``localhost:8090/`` whatever
works... Also, a database with the name **book_library** will be created for you.

You should have the credentials in `src/.env.example`.

Run DatabaseSeeder

- ``docker compose run --rm artisan db:seed``

With this command you will have:
- 1000 authors
- [1000..5000] books
-----------------------------------------------------------------------------------------------

#### Testing

To review the tests and check if they are working correctly:

1. Login as root user into phpmyadmin.
2. Create book_library_test_db.
3. In .env file add DB_TEST_DATABASE=book_library_test_db just below DB_PASSWORD=secret

- docker compose run --rm artisan test
