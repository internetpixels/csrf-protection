# CSRF Protection

Protect your web app from malicious requests by using CSRF protection tokens in links and forms. This CSRF protection library does not use sessions, files, memory storage or databases.

[![License](https://camo.githubusercontent.com/cf76db379873b010c163f9cf1b5de4f5730b5a67/68747470733a2f2f6261646765732e66726170736f66742e636f6d2f6f732f6d69742f6d69742e7376673f763d313032)](https://github.com/internetpixels/csrf-protection)
[![Build Status](https://travis-ci.org/internetpixels/csrf-protection.svg)](https://travis-ci.org/internetpixels/csrf-protection)
[![Maintainability](https://api.codeclimate.com/v1/badges/d0d817a21ca7243433b3/maintainability)](https://codeclimate.com/github/internetpixels/csrf-protection)

## Basic examples

In the examples below you'll find the 3 most important methods:
 - ``TokenManager::create();``
 - ``TokenManager::createHtmlField();``
 - ``TokenManager::validate();``

_The full namespace is ``InternetPixels\CSRFProtection\TokenManager``._

### Setup the TokenManager
You have to set some settings for a secure TokenManager. Please overwrite the salt by your own one.
```php
<?php
TokenManager::setSalt('P*17OJznMttaR#Zzwi4YhAY!H7hPGUCd', 'ERGirehgr4893ur43tjrg98rut98ueowifj');
TokenManager::setUserId(7);
TokenManager::setSessionToken('session_token');
```

### Create a safe form
The TokenManager can improve the security  
```php
<form action="/your/page" method="post">
    <?= TokenManager::createHtmlField('my_form') ?>
</form>
```

### Create a safe link
When a user wants to delete an item in your application, you want to be sure that the request is valid. Create a token and add it in the link to your delete page.
```html
<a href="/posts/delete/1?token=<?= TokenManager::create('delete_post') ?>">Delete Post</a>
```
In the delete action you want to validate the token with the ``TokenManager::validate()`` method.

### Validate the user input
Once a user is posting the form data to your server(s), you'll first need to validate the given token. By default, the field name used is ``_token``.
```php
<?php

if( filter_has_var(INPUT_POST, '_token') ) {
    if( TokenManager::validate('my_form', filter_input(INPUT_POST, '_token')) ) {
        // valid token
    }
    else {
        // invalid token
    }
}
```