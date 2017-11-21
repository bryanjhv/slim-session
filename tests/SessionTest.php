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
    public function testConstructor($handler)
    {
        $session = new SessionStub(['handler' => $handler]);
        $this->assertInstanceOf(Session::class, $session);

        return $session;
    }

    /**
     * @group passed
     * @expectedException \Exception
     */
    public function testHandlerErrorExceptipn()
    {
        $session = new SessionStub(['handler' => \stdClass::class]);
    }

     /**
     * @group passed
     * @expectedException \Exception
     */
    public function testHandlerNotFoundExceptipn()
    {
        $session = new SessionStub(['handler' => 'WTF???']);
    }

    /**
     * @group passed
     * @dataProvider handlerProvider
     */
    public function testStartSession($handler)
    {
        $session = new SessionStub(['handler' => $handler]);
        $handler = $session->startSession();
        $this->assertInstanceOf(Handler::class, $handler);
    }

    /**
     * @return mixed
     */
    public function handlerProvider()
    {
        return [
            [ HandlerStub::class ]
        ];   
    }
}