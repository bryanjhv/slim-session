<?php
namespace Slim\Middleware;

use SessionHandlerInterface as Handler;

trait HandlerTrait
{
    protected function registHandler(Handler $handler)
    {
        if (PHP_VERSION_ID >= 50400) {
            session_set_save_handler($handler, true);
        } else {
            session_set_save_handler(
                array($handler, 'open'),
                array($handler, 'close'),
                array($handler, 'read'),
                array($handler, 'write'),
                array($handler, 'destroy'),
                array($handler, 'gc')
            );
        }
    }
}