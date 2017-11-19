<?php
namespace Tests;

use Tests\Stub\SessionStub;
use Tests\Stub\SessionHandlerStub as HandlerStub;
use Slim\Middleware\Session;
use SessionHandlerInterface as Handler;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group passed
     * @dataProvider handlerProvider
     */
    public function testConstructor(Handler $handler)
    {
        $session = new SessionStub(array('handler' => $handler));
        $this->assertInstanceOf(Session::class, $session);

        return $session;
    }

    /**
     * @group passed
     * @expectedException \Exception
     */
    public function testHandlerErrorExceptipn()
    {
        $session = new SessionStub(array('handler' => new \stdClass()));
        $session->startSession();
    }

    /**
     * @group passed
     * @dataProvider handlerProvider
     */
    public function testStartSession(Handler $handler)
    {
        $session = new SessionStub(array('handler' => $handler));
        $session->startSession();
    }

    /**
     * @return mixed
     */
    public function handlerProvider()
    {
        return [
            [ new HandlerStub ]
        ];   
    }
}