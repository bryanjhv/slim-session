<?php
namespace Tests\Stub;

use SessionHandlerInterface as Handler;

class SessionHandlerStub implements Handler
{
    public function  close (  )
    {
        return;
    }
    public function  destroy (  $session_id )
    {
        return true;
    }
    public function  gc (  $maxlifetime )
    {
        return true;
    }
    public function  open (  $save_path ,  $session_name )
    {
        return true;
    }
    public function  read (  $session_id )
    {
        return '';
    }
    public function  write (  $session_id ,  $session_data )
    {
        return true;
    }
}