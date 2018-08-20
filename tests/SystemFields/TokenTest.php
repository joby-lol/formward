<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
declare(strict_types=1);
namespace Formward\SystemFields;

use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    use \SteveGrunwell\PHPUnit_Markup_Assertions\MarkupAssertionsTrait;

    public function setUp()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'phpunit';
        $_SERVER['REMOTE_ADDR'] = '1.1.1.1';
        $_SESSION = $_REQUEST = $_GET = $_POST = array();
    }

    public function testNaming()
    {
        $token = new Token('Test token', 'token');
        $this->assertEquals('_token', $token->name());
    }

    public function testStorageConsistency()
    {
        $token = new Token('Consistency test');
        $value = $token->value();
        $token = new Token('Consistency test');
        $this->assertEquals($value, $token->value());
        $token2 = new Token('Inconsistency test');
        $this->assertNotEquals($value, $token2->value());
    }

    public function testExpiration()
    {
        $token = new Token('Test');
        $value = $token->value();
        $this->assertTrue($token->test($value));
        $this->assertTrue($token->test($value));
        $token->clear();
        $this->assertFalse($token->test($value));
    }
}
