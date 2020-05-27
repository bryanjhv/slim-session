<?php

namespace Slim\Middleware;

use SlimSession\Cookie;

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
class Session extends \Slim\Middleware
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
    public function __construct($settings = array())
    {
        $defaults = array(
            'lifetime' => '20 minutes',
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => false,
            'samesite' => '',
            'name' => 'slim_session',
            'autorefresh' => false,
            'ini_settings' => array(),
        );
        $settings = array_merge($defaults, $settings);
        if (is_string($lifetime = $settings['lifetime'])) {
            $settings['lifetime'] = strtotime($lifetime) - time();
        }
        $this->settings = $settings;

        $this->iniSet($settings['ini_settings']);
        // Just override this, to ensure package is working
        if (ini_get('session.gc_maxlifetime') < $settings['lifetime']) {
            $this->iniSet(array(
                'session.gc_maxlifetime' => $settings['lifetime'] * 2,
            ));
        }
    }

    /**
     * Called when middleware needs to be executed.
     */
    public function call()
    {
        $this->startSession();
        $this->next->call();
    }

    /**
     * Start session
     */
    protected function startSession()
    {
        if (session_id() !== '') {
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
