# slim-session

Simple middleware for [Slim Framework 4][slim], that allows managing PHP
built-in sessions and includes a `Helper` class to help you with the `$_SESSION`
superglobal.

**For the middleware version for Slim Framework 3, please check out the `slim-3`
branch in this repository.**

**For the middleware version for Slim Framework 2, please check out the `slim-2`
branch in this repository.**

## Installation

Add this line to `require` block in your `composer.json`:

```json
"bryanjhv/slim-session": "~4.0"
```

Or, run in a shell instead:

```sh
composer require bryanjhv/slim-session:~4.0
```

## Usage

```php
$app = \Slim\Factory\AppFactory::create();
$app->add(
  new \Slim\Middleware\Session([
    'name' => 'dummy_session',
    'autorefresh' => true,
    'lifetime' => '1 hour',
  ])
);
```

### Supported options

- `lifetime`: How much should the session last? Default `20 minutes`. Any
  argument that `strtotime` can parse is valid.
- `path`, `domain`, `secure`, `httponly`, `samesite`: Options for the session
  cookie. Please note that `samesite` is `'Lax'` by default, set to `''` to
  disable.
- `name`: Name for the session cookie. Defaults to `slim_session` (instead of
  PHP's `PHPSESSID`).
- **`autorefresh`**: `true` if you want session to be refresh when user activity
  is made (interaction with server).
- `handler`: Custom session handler class or object. Must implement
  `SessionHandlerInterface` as required by PHP.
- `ini_settings`: Associative array of custom [session configuration][sesscfg].
  Previous versions of this package had some hardcoded values which could bring
  serious performance leaks (see #30):
  ```php
  [
    'session.gc_divisor' => 1,
    'session.gc_probability' => 1,
    'session.gc_maxlifetime' => 30 * 24 * 60 * 60,
  ];
  ```

## Session helper

A `Helper` class is available, which you can register globally or instantiate:

```php
$container = new \DI\Container();

// Register globally to app
$container->set('session', function () {
  return new \SlimSession\Helper();
});
\Slim\Factory\AppFactory::setContainer($container);
```

That will provide `$app->get('session')`, so you can do:

```php
$app->get('/', function ($req, $res) {
  // or $this->get('session') if registered
  $session = new \SlimSession\Helper();

  // Check if variable exists
  $exists = $session->exists('my_key');
  $exists = isset($session->my_key);
  $exists = isset($session['my_key']);

  // Get variable value
  $my_value = $session->get('my_key', 'default');
  $my_value = $session->my_key;
  $my_value = $session['my_key'];

  // Set variable value
  $app->get('session')->set('my_key', 'my_value');
  $session->my_key = 'my_value';
  $session['my_key'] = 'my_value';

  // Merge value recursively
  $app->get('session')->merge('my_key', ['first' => 'value']);
  $session->merge('my_key', ['second' => ['a' => 'A']]);
  $letter_a = $session['my_key']['second']['a']; // "A"

  // Delete variable
  $session->delete('my_key');
  unset($session->my_key);
  unset($session['my_key']);

  // Destroy session
  $session::destroy();

  // Get session id
  $id = $this->session::id();

  return $res;
});
```

## Contributors

[Here][contributors] are the big ones listed. :smile:

## TODO

- Complete `Helper` tests. (thanks @Zemistr)
- Slim-specific tests (integration with Slim App).

## License

MIT

[slim]: https://www.slimframework.com/docs/v4/
[sesscfg]: https://www.php.net/manual/en/session.configuration.php
[contributors]: https://github.com/bryanjhv/slim-session/graphs/contributors
