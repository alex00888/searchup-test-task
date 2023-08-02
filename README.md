
## SearchUp test task

This API backend shows exchange rates from cbr.ru

## Installation

- Clone this repo
- Install dependencies by running `composer install`
- Copy `.env.example` to `.env`
- Generate a new application key by running command `./artisan key:generate`
- Configure MySQL and Redis connection in the `.env` file (MySQL for exchange rates, Redis for the jobs queue)
- Run database migrations using command `./artisan migrate`
- Configure domain (or '/etc/hosts' file for local deployment)
- Configure web server
  - Example of local Nginx virtual host configuration:
```
server {
  listen          80;
  server_name     searchup.local;

  location / {
    include fastcgi.conf;
    fastcgi_param SCRIPT_FILENAME /home/alex/projects/test/searchup/back/public/index.php;
    fastcgi_pass unix:/var/run/php/php-fpm.sock;
  }
}
```

## Running

- Use the API url, for example `/api/exchange-rate/2023-08-01/USD/EUR` to see exchange rates. It will also cache the data.

### Pre fetching exchange rates

- Run command to pre-fetch rates using command `./artisan app:pre-fetch-rates`, it will put jobs to the queue
- Run queue job service using command `./artisan queue:work --tries=1`, it will execute jobs from the queue

### Helpful commands

- Use command `./artisan queue:monitor default` to check the current queue length