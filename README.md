# Instructions

- Clone project

```git clone https://github.com/serega170584/xm.git```

- Run docker-compose

```docker-compose up -d```

- Install composer, npm packages, fill database with companies, history from remote services, run migrations

```docker-compose exec app sh```

```composer install```

```npm run dev```

```php bin/console app:api:company:import```

```php bin/console app:api:history:import```

```php bin/console doctrine:migrations:migrate```

- Visit company form via browser

```http://localhost:3100/company```

- Visit company history via browser

```http://localhost:3100/history```





    
        