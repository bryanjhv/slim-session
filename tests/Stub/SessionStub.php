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
                //Mock: @session_set_save_handler($handler, true);
                return;
            } else {
                throw new \Exception(sprintf("SessionHandlerInterface expected, %s given", get_class($handler)));
            }
        }
    }
}