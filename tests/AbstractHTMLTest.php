<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
declare(strict_types=1);
namespace Formward;

use PHPUnit\Framework\TestCase;

class AbstractHTMLTest extends TestCase
{
    use \SteveGrunwell\PHPUnit_Markup_Assertions\MarkupAssertionsTrait;

    public function testBasics()
    {
        $object = new AbstractHTMLHarness();
        $this->assertContainsSelector('div', $object);
        $object->attributes = array('class'=>'tc','id'=>'tid');
        $this->assertContainsSelector('#tid', $object);
        $this->assertContainsSelector('.tc', $object);
        $object->content = 'content text';
        $this->assertElementContains('content text', '#tid', $object);
    }
}

class AbstractHtmlHarness extends AbstractHTML
{
    public $tag = 'div';
    public $selfClosing = false;
    public $attributes = array();
    public $content = null;

    protected function htmlTag()
    {
        return $this->tag;
    }
    protected function htmlTagSelfClosing()
    {
        return $this->selfClosing;
    }
    protected function htmlAttributes()
    {
        return $this->attributes;
    }
    protected function htmlTagContent()
    {
        return $this->content;
    }
}
