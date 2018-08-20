<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
declare(strict_types=1);
namespace Formward;

use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    public function testBasics()
    {
        $harness = new ValueFunctionTraitHarness();
        //test defaults
        $this->assertNull($harness->a());
        $this->assertEquals('b default', $harness->b());
        //set a and retest
        $harness->a('a set');
        $this->assertEquals('a set', $harness->a());
        $this->assertEquals('b default', $harness->b());
        //set b and retest
        $harness->b('b set');
        $this->assertEquals('a set', $harness->a());
        $this->assertEquals('b set', $harness->b());
    }
}

class ValueFunctionTraitHarness
{
    use ValueFunctionTrait;
    public function a($set = null)
    {
        return $this->valueFunction('a', $set);
    }
    public function b($set = null)
    {
        return $this->valueFunction('b', $set, 'b default');
    }
}
