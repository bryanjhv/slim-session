<?php
namespace Tests\Stub;

use Slim\Middleware\Session;
use SessionHandlerInterface as Handler;

class SessionStub extends Session
{
     /**
     * Start session
     */
    public function startSession()
    {   
        $settings = $this->settings;
        $name = $settings['name'];
        $handler = $settings['handler'];
        if ($handler) {
            if ($handler instanceof Handler) {
                $this->registHandler($handler);
            } else {
                throw new \Exception(sprintf("SessionHandlerInterface expected, %s given", get_class($handler)));
            }
        }

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
            // session_start();
        }
    }
}