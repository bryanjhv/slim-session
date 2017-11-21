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

        return $handler;
    }
}