<?php

use PHPUnit\Framework\TestCase;
use SlimSession\Helper;

session_start();

class HelperTest extends TestCase
{
    protected function setUp()
    {
        $_SESSION = [];
    }

    public function testExists()
    {
        $helper = new Helper;

        $_SESSION = $data = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

        $this->assertTrue($helper->exists('a'));
        $this->assertFalse($helper->exists('aa'));

        $this->assertTrue(isset($helper->b));
        $this->assertFalse(isset($helper->bb));

        $this->assertTrue(isset($helper['c']));
        $this->assertFalse(isset($helper['cc']));
    }

    public function testSet()
    {
        $helper = new Helper;

        $helper->set('a', 'A');
        $this->assertSame(['a' => 'A'], $_SESSION);

        $helper->b = 'B';
        $this->assertSame(['a' => 'A', 'b' => 'B'], $_SESSION);

        $helper['c'] = 'C';
        $this->assertSame(['a' => 'A', 'b' => 'B', 'c' => 'C'], $_SESSION);
    }

    public function testMerge()
    {
        $helper = new Helper;
        $helper->set('a', []);

        $helper->merge('a', ['a' => 'A']);
        $this->assertSame(['a' => ['a' => 'A']], $_SESSION);

        $helper->merge('a', ['b' => ['a' => 'A']]);
        $this->assertSame(['a' => ['a' => 'A', 'b' => ['a' => 'A']]], $_SESSION);

        $helper->merge('a', ['b' => ['b' => 'B']]);
        $this->assertSame(['a' => ['a' => 'A', 'b' => ['a' => 'A', 'b' => 'B']]], $_SESSION);
    }

    public function testGet()
    {
        $helper = new Helper;

        $_SESSION = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

        $this->assertSame('A', $helper->get('a'));
        $this->assertNull($helper->get('aa'));
        $this->assertSame('AAA', $helper->get('aaa', 'AAA'));

        $this->assertSame('B', $helper->b);
        $this->assertNull($helper->bb);

        $this->assertSame('C', $helper['c']);
        $this->assertNull($helper['cc']);
    }

    public function testDelete()
    {
        $helper = new Helper;

        $_SESSION = $data = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

        $helper->delete('A');
        $this->assertSame($data, $_SESSION);

        $helper->delete('a');
        unset($data['a']);
        $this->assertSame($data, $_SESSION);

        unset($helper->b, $data['b']);
        $this->assertSame($data, $_SESSION);

        unset($helper['c'], $data['c']);
        $this->assertSame($data, $_SESSION);
    }

    public function testClear()
    {
        $helper = new Helper;

        $_SESSION = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

        $helper->clear();
        $this->assertEmpty($_SESSION);
    }

    /**
     * @runInSeparateProcess
     */
    public function testId()
    {
        $helper = new Helper;

        $this->assertSame(session_id(), $helper::id());
        $this->assertNotSame(session_id(), $sessionId = $helper::id(true));
        $this->assertSame(session_id(), $sessionId);
    }

    /**
     * @runInSeparateProcess
     */
    public function testDestroy()
    {
        $helper = new Helper;

        $_SESSION = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

        $this->assertNotEmpty($helper::id());
        $this->assertNotEmpty(session_id());
        $this->assertNotEmpty($_SESSION);

        $helper::destroy();

        $this->assertEmpty($helper::id());
        $this->assertEmpty(session_id());
        $this->assertEmpty($_SESSION);

        $this->markTestIncomplete('Please finish test for "::destroy".');
    }

    public function testCount()
    {
        $helper = new Helper;

        $_SESSION = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

        $this->assertCount($helper->count(), $_SESSION);
    }

    public function testIterator()
    {
        $helper = new Helper;

        $_SESSION = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

        $this->assertInstanceOf(Iterator::class, $helper->getIterator());
        $this->assertSame($_SESSION, iterator_to_array($helper->getIterator()));
    }
}
