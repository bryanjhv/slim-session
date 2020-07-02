<?php

namespace Slim\Middleware;

use SlimSession\Cookie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Session middleware
 *
 * This class is meant to provide a easy way to manage sessions with framework,
 * using the PHP built-in (native) sessions but also allowing to manipulate the
 * session variables via same app instance, by registering a container to the
 * helper class that ships with this package. As a plus, you can set a lifetime
 * for a session and it will be updated after each user activity or interaction
 * like an 'autorefresh' feature.
 *
 * Keep in mind this relies on PHP native sessions, so for this to work you must
 * have that enabled and correctly working.
 *
 * @package Slim\Middleware
 * @author  Bryan Horna
 */
class Session
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * Constructor
     *
     * @param array $settings
     */
    public function __construct($settings = [])
    {
        $defaults = [
            'lifetime' => '20 minutes',
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => false,
            'samesite' => 'Lax',
            'name' => 'slim_session',
            'autorefresh' => false,
            'handler' => null,
            'ini_settings' => [],
        ];
        $settings = array_merge($defaults, $settings);

        if (is_string($lifetime = $settings['lifetime'])) {
            $settings['lifetime'] = strtotime($lifetime) - time();
        }
        $this->settings = $settings;

        $this->iniSet($settings['ini_settings']);
        // Just override this, to ensure package is working
        if (ini_get('session.gc_maxlifetime') < $settings['lifetime']) {
            $this->iniSet([
                'session.gc_maxlifetime' => $settings['lifetime'] * 2,
            ]);
        }
    }

    /**
     * Called when middleware needs to be executed.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler PSR7 handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(
        Request $request,
        RequestHandler $handler
    ): Response {
        $this->startSession();

        return $handler->handle($request);
    }

    /**
     * Start session
     */
    protected function startSession()
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $settings = $this->settings;
        $name = $settings['name'];

        Cookie::setup($settings);

        // Refresh session cookie when "inactive",
        // else PHP won't know we want this to refresh
        if ($settings['autorefresh'] && isset($_COOKIE[$name])) {
            Cookie::set(
                $name,
                $_COOKIE[$name],
                time() + $settings['lifetime'],
                $settings
            );
        }

        session_name($name);

        $handler = $settings['handler'];
        if ($handler) {
            if (!($handler instanceof \SessionHandlerInterface)) {
                $handler = new $handler();
            }
            session_set_save_handler($handler, true);
        }

        session_cache_limiter('');
        session_start();
    }

    protected function iniSet($settings)
    {
        foreach ($settings as $key => $val) {
            if (strpos($key, 'session.') === 0) {
                ini_set($key, $val);
            }
        }
    }
}
