<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
declare(strict_types=1);
namespace Formward;

include_once __DIR__.'/fake-session.php';

use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    use \SteveGrunwell\PHPUnit_Markup_Assertions\MarkupAssertionsTrait;

    public function setUp()
    {
        //fake user-agent and IP
        $_SERVER['HTTP_USER_AGENT'] = 'phpunit';
        $_SERVER['REMOTE_ADDR'] = '1.1.1.1';
        //fake session storage
        $GLOBALS['FAKESESSID'] = null;
        $GLOBALS['FAKESESSIONS'] = array();
    }

    public function testSubmit()
    {
        //form with CSRF protection enabled
        $f = new Form('Form with CSRF');
        $this->assertFalse($f->submitted());
        $f = new Form('Form with CSRF');
        $_GET[$f->tokenName()] = $f->tokenValue();
        $this->assertFalse($f->submitted());
        $f = new Form('Form with CSRF');
        $_REQUEST[$f->tokenName()] = $f->tokenValue();
        $this->assertFalse($f->submitted());
        $f = new Form('Form with CSRF');
        $_POST[$f->tokenName()] = $f->tokenValue();
        $this->assertTrue($f->submitted());
        //form without CSRF protection enabled
        $f = new Form('Form without CSRF');
        $f->csrf(false);
        $f2 = new Form('Another form without CSRF');
        $f2->csrf(false);
        //$f and $f2 should have the same token, because it's just a dumb submit check now
        $this->assertEquals($f->tokenValue(), $f2->tokenValue());
        //same assertions as above
        $f = new Form('Form without CSRF');
        $f->csrf(false);
        $this->assertFalse($f->submitted());
        $f = new Form('Form without CSRF');
        $f->csrf(false);
        $_GET[$f->tokenName()] = $f->tokenValue();
        $this->assertFalse($f->submitted());
        $f = new Form('Form without CSRF');
        $f->csrf(false);
        $_REQUEST[$f->tokenName()] = $f->tokenValue();
        $this->assertFalse($f->submitted());
        $f = new Form('Form without CSRF');
        $f->csrf(false);
        $_POST[$f->tokenName()] = $f->tokenValue();
        $this->assertTrue($f->submitted());
    }

    public function testOneTimeTokens()
    {
        //with one time tokens on, two identical forms should not be able to use the smame token value
        $f1 = new Form('form with oneTimeTokens on');
        $_POST[$f1->tokenName()] = $f1->tokenValue();
        $this->assertTrue($f1->submitted());
        $f2 = new Form('form with oneTimeTokens on');
        $this->assertFalse($f2->submitted());
        //with one time tokens off, two identical forms should be able to use the smame token value
        $f1 = new Form('form with oneTimeTokens off');
        $f1->oneTimeTokens(false);
        $_POST[$f1->tokenName()] = $f1->tokenValue();
        $this->assertTrue($f1->submitted());
        $f2 = new Form('form with oneTimeTokens off');
        $f2->oneTimeTokens(false);
        $this->assertTrue($f2->submitted());
    }

    protected $validFnCalled = false;
    protected $invalidFnCalled = false;
    protected $notSubmittedFnCalled = false;

    protected function resetCallbacks()
    {
        $this->validFnCalled = false;
        $this->invalidFnCalled = false;
        $this->notSubmittedFnCalled = false;
    }

    public function testHandle()
    {
        //set up harness callbacks
        $validFn = function ($form) {
            $this->validFnCalled = true;
        };
        $invalidFn = function ($form) {
            $this->invalidFnCalled = true;
        };
        $notSubmittedFn = function ($form) {
            $this->notSubmittedFnCalled = true;
        };
        //form not submitted
        $this->resetCallbacks();
        $f = new Form('Form with CSRF');
        $f['i'] = new Fields\Input('input field');
        $this->assertFalse($f->submitted());
        $this->assertNull(
            $f->handle($validFn, $invalidFn, $notSubmittedFn)
        );
        $this->assertFalse($this->validFnCalled);
        $this->assertFalse($this->invalidFnCalled);
        $this->assertTrue($this->notSubmittedFnCalled);
        //form not submitted with validation error
        $this->resetCallbacks();
        $f = new Form('Form with CSRF');
        $f['i'] = new Fields\Input('input field');
        $f['i']->addValidatorFunction('false', function ($field) {
            return 'error message';
        });
        $this->assertFalse($f->submitted());
        $this->assertNull(
            $f->handle($validFn, $invalidFn, $notSubmittedFn)
        );
        $this->assertFalse($this->validFnCalled);
        $this->assertFalse($this->invalidFnCalled);
        $this->assertTrue($this->notSubmittedFnCalled);
        //form submitted with validation error
        $this->resetCallbacks();
        $f = new Form('Form with CSRF');
        $f['i'] = new Fields\Input('input field');
        $f['i']->addValidatorFunction('false', function ($field) {
            return 'error message';
        });
        $_POST[$f->tokenName()] = $f->tokenValue();
        $this->assertTrue($f->submitted());
        $this->assertFalse(
            $f->handle($validFn, $invalidFn, $notSubmittedFn)
        );
        $this->assertFalse($this->validFnCalled);
        $this->assertTrue($this->invalidFnCalled);
        $this->assertFalse($this->notSubmittedFnCalled);
        //form  submitted without validation error
        $this->resetCallbacks();
        $f = new Form('Form with CSRF');
        $f['i'] = new Fields\Input('input field');
        $_POST[$f->tokenName()] = $f->tokenValue();
        $this->assertTrue($f->submitted());
        $this->assertTrue(
            $f->handle($validFn, $invalidFn, $notSubmittedFn)
        );
        $this->assertTrue($this->validFnCalled);
        $this->assertFalse($this->invalidFnCalled);
        $this->assertFalse($this->notSubmittedFnCalled);
    }

    public function testHandleMissingArgs()
    {
        //set up harness callbacks
        $validFn = function ($form) {
            $this->validFnCalled = true;
        };
        $invalidFn = function ($form) {
            $this->invalidFnCalled = true;
        };
        $notSubmittedFn = function ($form) {
            $this->notSubmittedFnCalled = true;
        };
        //form not submitted
        $this->resetCallbacks();
        $f = new Form('Form with CSRF');
        $f['i'] = new Fields\Input('input field');
        $this->assertFalse($f->submitted());
        $this->assertNull(
            $f->handle($validFn, $invalidFn)
        );
        $this->assertFalse($this->validFnCalled);
        $this->assertFalse($this->invalidFnCalled);
        //form submitted with validation error
        $this->resetCallbacks();
        $f = new Form('Form with CSRF');
        $f['i'] = new Fields\Input('input field');
        $f['i']->addValidatorFunction('false', function ($field) {
            return 'error message';
        });
        $_POST[$f->tokenName()] = $f->tokenValue();
        $this->assertTrue($f->submitted());
        $this->assertFalse(
            $f->handle($validFn, null, $notSubmittedFn)
        );
        $this->assertFalse($this->validFnCalled);
        $this->assertFalse($this->notSubmittedFnCalled);
        //form  submitted without validation error
        $this->resetCallbacks();
        $f = new Form('Form with CSRF');
        $f['i'] = new Fields\Input('input field');
        $_POST[$f->tokenName()] = $f->tokenValue();
        $this->assertTrue($f->submitted());
        $this->assertTrue(
            $f->handle(null, $invalidFn, $notSubmittedFn)
        );
        $this->assertFalse($this->invalidFnCalled);
        $this->assertFalse($this->notSubmittedFnCalled);
    }
}
