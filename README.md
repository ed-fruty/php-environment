# PHP-Environment
PHP Environment Wrapper to load environment variables [supported json, php, ini, xml, yml and  serialized data formats]

Why ed-fruty/php-environment?
-----------------------------

**You should never store sensitive credentials in your code**. Storing
[configuration in the environment](http://www.12factor.net/config) is one of
the tenets of a [twelve-factor app](http://www.12factor.net/). Anything that is
likely to change between deployment environments – such as database credentials
or credentials for 3rd party services – should be extracted from the
code into environment variables.

Basically, an environment file is an easy way to load custom configuration
variables that your application needs without having to modify .htaccess
files or Apache/nginx virtual hosts. This means you won't have to edit
any files outside the project, and all the environment variables are
always set no matter how you run your project - Apache, Nginx, CLI, and
even PHP 5.4's built-in webserver. This way is easier than all the other
ways you know of to set environment variables, and you're going to love
it.

* NO editing virtual hosts in Apache or Nginx
* NO adding `php_value` flags to .htaccess files
* EASY portability and sharing of required ENV values
* COMPATIBLE with PHP's built-in web server and CLI runner


Installation with Composer
--------------------------

```shell

composer require ed-fruty/php-environment

```

Usage
-----
An environment file is generally kept out of version control since it can contain
sensitive API keys and passwords. You can create the example file (`env.json.example`)
with all the required environment variables defined except for the sensitive
ones, which are either user-supplied for their own development environments or
are communicated elsewhere to project collaborators. The project collaborators
then independently copy an environment file to a local `env.json` and ensure
all the settings are correct for their local environment, filling in the secret
keys or providing their own values when necessary. In this usage, the `env.json`
file should be added to the project's `.gitignore` file so that it will never
be committed by collaborators.  This usage ensures that no sensitive passwords
or API keys will ever be in the version control history so there is less risk
of a security breach, and production values will never have to be shared with
all project collaborators.

Add your application configuration to your environment file anywhere, but basically put it in the root of your
project. **Make sure an environment file is added to your `.gitignore` so it is not
checked-in the code**

Simple exmaple
--------------

Create a file `env.json` with the same content 

```json
{
  "database" : {
    "connection" : {
      "host"      : "localhost",
      "username"  : "root",
      "password"  : "mySuperPassword",
      "port"      : "3310"
    }
  }
}
```

Now you need to load environment configuration in your code. 

Here we go!

```php
use Fruty\Environment\Env;

Env::instance()->load(__DIR__, 'env');
```

All done!

`env` argument is the environment filename. 
`__DIR__` is the path where file is located. Default file extension is `json`,
but you can use others (see below).
Note. Maybe instead of `__DIR__` you need to set path, where you put your envrionment file.

Now you can use 

```php
echo Env::instance()->get('databse.connection.host'); // Will print 'localhost'
var_dump(Env::instance()->get('database.connection'));
/*
 * object(stdClass)#3 (4) {
 *      ["host"]=>
 *      string(9) "localhost"
 *      ["username"]=>
 *      string(4) "root"
 *      ["password"]=>
 *      string(15) "mySuperPassword"
 *      ["port"]=>
 *      string(4) "3310"
 *    }
 */
```

You can access to your envrionment variables with the different ways. Next examples are alternatives to each other.

```php
$result = Env::instance('database');
$result = $_ENV['database'];   // global php array, has limits (see below)
$result = getenv('database');  // standart php function, has limits (see below)
$result = env_get('database');
$result = envGet('database');
```

As you can se, we use package helper functions `env_get` and `envGet`. They are short alternatives for `Env::instance()->get()`.

Get all environment variables
-----------------------------

```php
$env = Env::instance()->get();
```


Default Values
--------------

To get some value or set or set defaults, put default values as the second argument

```php
$databaseUser = env_get('database.connection.username', 'myDbUser');

```

This example shows, `$databaseUser` value will be value from environment file, or 'myDbUser' by defaults if it not exists 
in the environment file

Also you can use (but not recommended) somethig like this:

```php
echo Env::instance()->get('database')->connection->host; // Will print 'localhost'
echo $_ENV['database']->connection->host; // will print 'localhost'
```

Limits for $_ENV and getenv()
---------

```php
$_ENV['database.connection'];
getenv('database.connection');
```
will not work. For `$_ENV` and `getenv` function only first level of definition will be work (before dot '.'). 
And `getenv` function has one surprice. Php allow us to save by `putenv` only scalar values, so such code:

```php
$result = getenv('database');
```
Is not an object. I will be string with json_encoded values


Array keys not works ?
------------
Note, associative array will be object instance of stdClass

```php

  echo env_get('services')['database']; // Wrong
  echo env_get('services')->database; // Right

```


Auto-file detecting
-------------------

For example, many companies has such envrionment way. Every developer set `APP_ENV` value to his local machine
and environment filename loads with the same name.
For example my local `APP_ENV` value is `dev-fruty`, so next script will load variables from `dev-fruty.json` file.

```php

Env::instance()->load(__DIR__, null);
```

Thats all.

How to add APP_ENV to your local machine?

[Windows users](http://www.computerhope.com/issues/ch000549.htm)

[Ubuntu](https://help.ubuntu.com/community/EnvironmentVariables)

[Mac](http://apple.stackexchange.com/questions/106778/how-do-i-set-environment-variables-on-os-x)


Envrionment file not exists?
----------------------------
By default, if envrionment file not exists, `env_get('key')` will return null, or default value if it setted.
You can set

```php

Env::instance()->fileNotFoundException(true);
```

And `InvalidArgumentException` will throws if envrionment file not exists.


Required variables
------------------

Your can set not only that must be exists envronment file, also you can set requred variables definitions.

```php

Env::instance()->required([
  'appName',
  'cache',
  'database.connection.charset',
]);

```
Now if one of this params not exists in your envrioment file you will get `RuntimeException`.


Envriornment readers
--------------------

Early in examples we use only `json` format, but you can use such supported formats:
  - JSON
  - XMl
  - INI
  - PHP Array
  - Yml
  - Serialize

What you need to use it? put reader name as third argument, when loading your environment

```php

Env::instance()->load(__DIR__, 'env', 'ini');
Env::instance()->load(__DIR__, null, 'xml'); // for auto detecting your envrionment filename
```

JSON Reader example
-------------------
1. Create file `env.json` with the same content

```json
{
  "database" : {
    "connection" : {
      "host"      : "localhost",
      "username"  : "root",
      "password"  : "mySuperPassword",
      "port"      : "3310"
    }
  }
}
```

2. Load envrionment file

```php
Env::isntance()->load(__DIR__, 'env', 'json');
```

3. Use it.

```php
echo Env::instance('database.connection.host'); // will print 'localhost'
```

  
Ini Reader example
-------------------
1. Create file `env.ini` with the same content

```ini
appName=My Application Name

;commentedVariable=Value

[database_connection]
host=localhost
username=root
password=MySupperPassword
port=3306
```

2. Load envrionment file

```php
Env::isntance()->load(__DIR__, 'env', 'ini');
```

3. Use it.

```php

var_dump(env_get('database_connection'));

/* class stdClass#7 (4) {
 *  public $host =>
 *  string(9) "localhost"
 *  public $username =>
 *  string(4) "root"
 *  public $password =>
 *  string(16) "MySupperPassword"
 *  public $port =>
 *  string(4) "3306"
 */ }

echo env_get('commentedVariable'); // NULL
echo env_get('appName') // string(19) "My Application Name"

```

PHP Array Reader example
-------------------
1. Create file `env.php` with the same content

```php
<?php
return [
    'appName' => 'My Application Name',
    'services' => [
        'database' => [
            'connection_default' => [
                'host' => 'localhost',
                'username'  => 'root',
                'password'  => 'meGaPaSsword'
            ]
        ]
    ]
];
```

2. Load envrionment file

```php
Env::isntance()->load(__DIR__, 'env', 'php');
```

3. Use it.

```php

var_dump(env_get('services.database.connection_default'));

/* class stdClass#9 (3) {
 *  public $host =>
 *  string(9) "localhost"
 *  public $username =>
 *  string(4) "root"
 *  public $password =>
 *  string(12) "meGaPaSsword"
 */ }

```


Xml Reader example
-------------------
1. Create file `env.xml` with the same content

```xml
<?xml version="1.0"?>
<root>
    <database>
        <connection>
            <host>localhost</host>
            <database>db_name</database>
            <username>custom_user</username>
        </connection>
    </database>
</root>
```

2. Load envrionment file

```php
Env::isntance()->load(__DIR__, 'env', 'xml');
```

3. Use it.

```php

var_dump(env_get('database.connection'));

/* class stdClass#8 (3) {
 *  public $host =>
 *  string(9) "localhost"
 *  public $database =>
 *  string(7) "db_name"
 *  public $username =>
 *  string(11) "custom_user"
 */ }


```

Serialize Reader example
-------------------
1. Create file `env.serialize` with the same content

```
a:2:{s:7:"appName";s:19:"My Application Name";s:8:"services";a:1:{s:8:"database";a:1:{s:18:"connection_default";a:3:{s:4:"host";s:9:"localhost";s:8:"username";s:4:"root";s:8:"password";s:12:"meGaPaSsword";}}}}
```

2. Load envrionment file

```php
Env::isntance()->load(__DIR__, 'env', 'serialize');
```

3. Use it.

```php

var_dump(env_get('services.database.connection_default'));

/* class stdClass#9 (3) {
 *  public $host =>
 *  string(9) "localhost"
 *  public $username =>
 *  string(4) "root"
 *  public $password =>
 *  string(12) "meGaPaSsword"
 */ }


```

Yaml Reader example
-------------------

For using yml reader you need to install `Symfony Yaml` component firstly. So do:

```php
composer require symfony/yaml
```

Next, create file `env.yml` or `env.yaml` (in our example it will be `env.yaml`) with the same content

```yaml
database:
  host: localhost
  username: root

```

Load envrionment file

```php
Env::isntance()->load(__DIR__, 'env', 'yaml');
```

Now use it.

```php

var_dump(env_get('database'));

/* class stdClass#7 (2) {
 *  public $host =>
 *  string(9) "localhost"
 *  public $username =>
 *  string(4) "root"
 */ }

echo env_get('database.host'); // will print 'localhost'
```

Alternatives?
-------------

Yea! It is very cool package for alternative usage is [vlucas/phpdotenv ](https://github.com/vlucas/phpdotenv)

It is very simple, only one file `.env` you needs. But you need to assign your varialbes only in one format:

```
DB_HOST=localhost
DB_USER=root
```

And the data from `.env` file are parses by regular expressions.

Maybe it would be nice to parse data by [parse_ini_file](http://php.net/manual/en/function.parse-ini-file.php) function, for dotenv.

Make a choice for yourself.
