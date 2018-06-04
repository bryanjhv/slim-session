# slim-session &nbsp;&nbsp;&nbsp; [![Donate][paybtn]][paylnk]

Simple middleware for [Slim Framework 3][slim], that allows managing PHP
built-in sessions and includes a `Helper` class to help you with the `$_SESSION`
superglobal.

**For the middleware version for Slim Framework 2, please check out the `slim-2`
branch in this repository.**


## Installation

Add this line to `require` block in your `composer.json`:

```
"bryanjhv/slim-session": "~3.0"
```

Or, run in a shell instead:

```sh
composer require bryanjhv/slim-session:~3.0
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

* `lifetime`: How much should the session last? Default `20 minutes`. Any
  argument that `strtotime` can parse is valid.
* `path`, `domain`, `secure`, `httponly`: Options for the session cookie.
* `name`: Name for the session cookie. Defaults to `slim_session` (instead of
  PHP's `PHPSESSID`).
* **`autorefresh`**: `true` if you want session to be refresh when user activity
  is made (interaction with server).
* `handler`: Custom session handler class or object. Must implement
  `SessionHandlerInterface` as required by PHP.
* `ini_settings`: Associative array of custom [session configuration][sesscfg].
  Previous versions of this package had some hardcoded values which could bring
  serious performance leaks (see #30):
  ```php
  [
      'session.gc_divisor'     => 1,
      'session.gc_probability' => 1,
      'session.gc_maxlifetime' => 30 * 24 * 60 * 60,
  ]
  ```


## Session helper

A `Helper` class is available, which you can register globally or instantiate:

```php
$container = $app->getContainer();

// Register globally to app
$container['session'] = function ($c) {
  return new \SlimSession\Helper;
};
```

That will provide `$app->session`, so you can do:

```php
$app->get('/', function ($req, $res) {
  // or $this->session if registered
  $session = new \SlimSession\Helper;

  // Check if variable exists
  $exists = $session->exists('my_key');
  $exists = isset($session->my_key);
  $exists = isset($session['my_key']);

  // Get variable value
  $my_value = $session->get('my_key', 'default');
  $my_value = $session->my_key;
  $my_value = $session['my_key'];

  // Set variable value
  $app->session->set('my_key', 'my_value');
  $session->my_key = 'my_value';
  $session['my_key'] = 'my_value';

  // Merge value recursively
  $app->session->merge('my_key', ['first' => 'value']);
  $session->merge('my_key', ['second' => ['a' => 'A']]);
  $letter_a = $session['my_key']['second']['a'];  // "A"

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


[slim]: https://www.slimframework.com
[sesscfg]: http://php.net/manual/en/session.configuration.php
[contributors]: https://github.com/bryanjhv/slim-session/graphs/contributors

[paybtn]: https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif
[paylnk]: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DVB7SSMVSHGTN
