
## Run via Docker

1. Download the project (or clone using GIT)
2. copy `.env.example` into `.env` and configure database credentials
3. Run `composer install`
4. Run `php artisan key:gen`
5. Run `./vendor/bin/sail up`
6. Run `./vendor/bin/sail artisan migrate --seed`

## Run on Local

1. Download the project (or clone using GIT)
2. Copy `.env.example` into `.env` and configure database credentials
3. Run `composer install`
4. Run `php artisan key:gen`
5. Run migrations `php artisan migrate --seed`
6. Start local server by executing `php artisan serve`
