# luminaire
Simple Bug Tracking System 


## Installation instructions

Luminaire app use [Composer][2] to manage it dependencies.

- Clone https://github.com/olegpopadko/luminaire.git Luminaire Application project with

```bash
git clone https://github.com/olegpopadko/luminaire.git
```

- Go to luminaire app folder and run composer installation:

```bash
composer install --no-dev
```

- Create the database with the name specified on previous step (default name is "luminaire").

- Clear cache:

```bash  
php app/console cache:clear --env prod
```

- Dump assets:

```bash  
php app/console assetic:dump --env=prod --no-debug
```

- Configure crontab by adding next command for sending email:

```bash
php app/console swiftmailer:spool:send --env=prod
```
Istruction for command you can find on [How to Spool Emails][1] page

[1]:  http://symfony.com/doc/current/cookbook/email/spool.html
[2]:  http://getcomposer.org/
