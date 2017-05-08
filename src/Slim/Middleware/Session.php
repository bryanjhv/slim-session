<?php

namespace Slim\Middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

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
 * Keep in mind this relies on PHP native sessions, so for this to work you
 * must have that enabled and correctly working.
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
            'lifetime'    => '20 minutes',
            'path'        => '/',
            'domain'      => null,
            'secure'      => false,
            'httponly'    => false,
            'name'        => 'slim_session',
            'autorefresh' => false,
        ];
        $settings = array_merge($defaults, $settings);

        if (is_string($lifetime = $settings['lifetime'])) {
            $settings['lifetime'] = strtotime($lifetime) - time();
        }
        $this->settings = $settings;

        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 1);
        ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);
    }

    /**
     * Called when middleware needs to be executed.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $this->startSession();

        return $next($request, $response);
    }

    /**
     * Start session
     */
    protected function startSession()
    {
        $settings = $this->settings;
        $name = $settings['name'];

        session_set_cookie_params(
            $settings['lifetime'],
            $settings['path'],
            $settings['domain'],
            $settings['secure'],
            $settings['httponly']
        );

        $inactive = session_status() === PHP_SESSION_NONE;

        if ($inactive) {
            // Refresh session cookie when "inactive",
            // else PHP won't know we want this to refresh
            if ($settings['autorefresh'] && isset($_COOKIE[$name])) {
                setcookie(
                    $name,
                    $_COOKIE[$name],
                    time() + $settings['lifetime'],
                    $settings['path'],
                    $settings['domain'],
                    $settings['secure'],
                    $settings['httponly']
                );
            }
        }

        session_name($name);
        session_cache_limiter(false);
        if ($inactive) {
            session_start();
        }
    }
}
