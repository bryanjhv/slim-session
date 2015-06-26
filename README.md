# slim-session

Simple middleware for Slim Framework 2, that allows managing PHP built-in
sessions and includes a `Helper` class to help you with the `$_SESSION`
superglobal.

## Installation

Include this line in your `composer.json`:

```
"bryanjhv/slim-session": "~1.0"
```

## Usage

The namespace for the middleware is the same as normal, so:

```php
$app = new \Slim\Slim;
$app->add(new \Slim\Middleware\Session(
  'name' => 'dummy_session',
  'autorefresh' => true,
  'lifetime' => '1 hour'
));
```

### Supported options

* `lifetime`: How long a session can be. Defaults to `20 minutes` (yes, you can
  pass anything that can be parsed by `strtotime`).
* `path`, `domain`, `secure`, `httponly`: Options for the session cookie.
* `name`: Name for the session cookie. Defaults to `slim_session` (instead of
  PHP's `PHPSESSID`).
* **`autorefresh`**: Set this to `true` if you want the session to be refresh
  each time a user activity is made.

## Session helper

This package also ships a `Helper` class and registers it to `$app->session` so
you can do:

```php
$app->get('/', function () use ($app) {
  $session = new \SlimSession\Helper; // or $app->session
  
  // Get a variable
  $key = $session->get('key', 'default');
  $st = $session->st;
  
  // Set a variable
  $session->my_key = 'my_value';
  $app->session->set('a', 'var');
  
  // Remove variable
  $session->remove('a_var');
  
  // Destroy session
  $session::destroy();
  
  // Get current session id
  $id = $app->session::id();
});
```

## TODO

Tests!

## License

MIT
