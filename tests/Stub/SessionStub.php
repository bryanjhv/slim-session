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
        $handler = new $settings['handler'];
        if (!$handler instanceof Handler) {
            throw new \Exception(sprintf("%s expected, %s given", Handler::class, $settings['handler']));            
        }

        return $handler;
    }
}