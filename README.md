
## Run via Docker

1. Download the project (or clone using GIT)
2. copy `.env.example` into `.env` and configure database credentials
3. Run `composer install`
4. Run `./vendor/bin/sail up`
5. On your docker instance, copy `.env.example` into `.env` and configure database credentials
6. Run `./vendor/bin/sail composer install`
7. Run `./vendor/bin/sail artisan migrate --seed`

## Run on Local

1. Download the project (or clone using GIT)
2. copy `.env.example` into `.env` and configure database credentials
3. Run `composer install`
4. Run migrations `php artisan migrate --seed`
5. Start local server by executing `php artisan serve`
