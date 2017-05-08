# slim-session

Simple middleware for Slim Framework 3, that allows managing PHP built-in
sessions and includes a `Helper` class to help you with the `$_SESSION`
superglobal.

**For the middleware version for Slim Framework 2, please check out the `slim-2`
branch in this repository.**

## Installation

Include this line in your `composer.json`:

```
"bryanjhv/slim-session": "~3.0"
```

## Usage

```php
$app = new \Slim\App;
$app->add(new \Slim\Middleware\Session([
  'name' => 'dummy_session',
  'autorefresh' => true,
  'lifetime' => '1 hour'
]));
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

This package also ships with a `Helper` class which you need to register in the
container or instance as an object if you want to use it:

```php
$container = $app->getContainer();

$container['session'] = function ($c) {
  return new \SlimSession\Helper;
};
```

This will provide you `$app->session`, so you can simply do:

```php
$app->get('/', function () {
  $session = new \SlimSession\Helper; // or $this->session if registered

  // Check if a variable exists
  $exists = $session->exists('my_key');
  $exists = isset($session->my_key);
  $exists = isset($session['my_key']);

  // Get a variable
  $my_value = $session->get('my_key', 'default');
  $my_value = $session->my_key;
  $my_value = $session['my_key'];

  // Set a variable
  $app->session->set('my_key', 'my_value');
  $session->my_key = 'my_value';
  $session['my_key'] = 'my_value';

  // Remove variable
  $session->delete('my_key');
  unset($session->my_key);
  unset($session['my_key']);

  // Destroy session
  $session::destroy();

  // Get current session id
  $id = $this->session::id();
});
```

## TODO

- Complete `Helper` tests. (thanks @Zemistr)
- Slim-specific tests (integration with Slim App).

## License

MIT
