# PHP Swoole

This project is intended to be a template for others PHP with Swoole Projects 
and Pest as test runner


## How to use

### Start server

1. Init containers
2. Access localhost:8080

```console
docker compose up -d
```

### Run tests

1. Install dependencies
1. Run composer command to run tests

```console
docker compose run composer composer install
docker compose run composer composer test
```