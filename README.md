php-cas
=======

Uses the CAS login system to login users.  Will return the username, and set the superglobal variables for basic HTTP Authentication.

To use, first install composer:
```
curl -s https://getcomposer.org/installer | php
```

Then create a composer.json file which lists PHP-CAS as a dependency
```
{
    "require": {
        "cobookman/php-cas": "dev-master"
    }
}
```

Next, install php-cas, and all other dependencies (or required packages) by running:
```
php composer.phar install
```

In your application's index.php (or root file) add the following lines:

```
<?php
require 'vendor/autoload.php';

$cas = new cobookman\PHPCAS(array(
  'serviceURL' => 'http://critique.gatech.edu', 
  'casURL' => 'https://login.gatech.edu/cas'    
));
$username = $cas->auth();
//At this point user has been logged in or user redirected to login page (script is killed).
//username stored in:       $_SERVER["REMOTE_USER"]
//a fake password stored in the password field:
//  $_SERVER["PHP_AUTH_PW"] = hash('sha512', $username);
/*
  Application Code
  ....
*/
```

By running $cas->auth() the user is validated against CAS. If the user was not logged in, he is sent to the cas login page, and upon successful login is redirected back to this page (service URL).  Then the username is stored in the basic HTTP authentication variables : ```$_SERVER["REMOTE_USER"]```.

If you have any questions feel free to email me at cobookman [at] gmail [dot] com
