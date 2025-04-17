This is API implementation for the Book Library system using Laravel

Open terminal and clone the project.
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

