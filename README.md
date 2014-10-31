# Annuaire web php client

Php client for annuaire-web using compoment-installer.

If you don't know how to install plugin with components look at [this link](https://github.com/RobLoach/component-installer)

it work with composer you can add it to your `composer.json` file
with:

    "sion/annuaire-php-client": "dev-master"

Run `composer install` to install it.

### Example 

```php
    $client = new Sion\Annuaire\Client("client_id","client_credential");
    $client->usePasswordGrant("pseudo","password");
    $client->checkToken();
```
