<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
declare(strict_types=1);
namespace Formward\Fields;

use PHPUnit\Framework\TestCase;
use Formward\Validators\AbstractValidator;

class ContainerTest extends TestCase
{
    use \SteveGrunwell\PHPUnit_Markup_Assertions\MarkupAssertionsTrait;

    public function testNaming()
    {
        $_POST = array();
        $container = new Container('Container', 'con');
        $container['in'] = new Input('Input');
        $this->assertEquals('con_in', $container['in']->name());
        $container['sub'] = new Container('Sub-container');
        $container['sub']['in'] = new Input('Sub-container input');
        $this->assertEquals('con_sub_in', $container['sub']['in']->name());
        //test naming is tied properly to value retrieval
        $_POST = array(
            'con_in' => 'con_in value',
            'con_sub_in' => 'con_sub_in value'
        );
        $this->assertEquals('con_in value', $container['in']->submittedValue());
        $this->assertEquals('con_sub_in value', $container['sub']['in']->submittedValue());
        //test recursive value retrieval from containers
        $this->assertEquals(
            array(
                'in' => 'con_in value',
                'sub' => array(
                    'in' => 'con_sub_in value'
                )
            ),
            $container->submittedValue()
        );
        $this->assertEquals(
            array(
                'in' => 'con_in value',
                'sub' => array(
                    'in' => 'con_sub_in value'
                )
            ),
            $container->value()
        );
        //test recursive value and default retrieval from containers
        $_POST = array();
        $container->default(array(
            'in' => 'con_in default',
            'sub' => array(
                'in' => 'con_sub_in default'
            )
        ));
        $this->assertEquals('con_in default', $container['in']->default());
        $this->assertEquals('con_sub_in default', $container['sub']['in']->default());
        $this->assertEquals(
            array(
                'in' => 'con_in default',
                'sub' => array(
                    'in' => 'con_sub_in default'
                )
            ),
            $container->value()
        );
        $this->assertEquals(
            array(
                'in' => null,
                'sub' => array(
                    'in' => null
                )
            ),
            $container->submittedValue()
        );
    }

    public function testMarkup()
    {
        $_POST = array();
        $container = new Container('Container', 'con');
        $this->assertSelectorCount(1, 'div.Container', $container);
        $container['in'] = new Input('Input');
        $container['in2'] = new Input('Input 2');
        $container['sub'] = new Container('Sub-container');
        $this->assertSelectorCount(2, 'input.class-Input', $container);
        $this->assertSelectorCount(2, 'div.Container', $container);
        $container['sub']['in'] = new Input('Sub-container input');
        $this->assertSelectorCount(3, 'input.class-Input', $container);
        //test naming is tied properly to value retrieval
        $_POST = array(
            'con_in' => 'con_in value',
            'con_sub_in' => 'con_sub_in value'
        );
        $this->assertContainsSelector('#con_in[value="con_in value"]', $container);
        $this->assertContainsSelector('#con_sub_in[value="con_sub_in value"]', $container);
    }

    public function testMethodPropagation()
    {
        $_POST = array();
        $container = new Container('Container', 'con');
        $container['in'] = new Input('Input');
        $container['sub'] = new Container('Sub-container');
        $container['sub']['in'] = new Input('Sub-container input');
        $container->method('get');
        $this->assertEquals('get', $container->method());
        $this->assertEquals('get', $container['in']->method());
        $this->assertEquals('get', $container['sub']->method());
        $this->assertEquals('get', $container['sub']['in']->method());
    }

    public function testValidatorObjects()
    {
        //simple validator object that is always true
        $f = new Container('test field');
        $f->addValidator('true', new SimpleValidationHarness(true));
        $this->assertTrue($f->validate());
        $this->assertTrue($f->validated());
        $this->assertEquals(array(), $f->validationMessage());
        //simple validator object that is always false
        $f = new Container('test field');
        $f->addValidator('true', new SimpleValidationHarness(true));
        $f->addValidator('false', new SimpleValidationHarness(false));
        $this->assertFalse($f->validate());
        $this->assertFalse($f->validated());
        $this->assertEquals(['testfield'=>'simple validator message'], $f->validationMessage());
        //removing validator
        $f = new Container('test field');
        $f->addValidator('true', new SimpleValidationHarness(true));
        $f->addValidator('false', new SimpleValidationHarness(false));
        $f->removeValidator('false');
        $this->assertTrue($f->validate());
        $this->assertTrue($f->validated());
    }

    public function testValidatorFunctions()
    {
        //simple validator object that is always true
        $f = new Container('test field');
        $f->addValidatorFunction('true', function ($field) {
            return true;
        });
        $this->assertTrue($f->validate());
        $this->assertTrue($f->validated());
        $this->assertEquals(array(), $f->validationMessage());
        //simple validator object that is always false
        $f = new Container('test field');
        $f->addValidatorFunction('true', function ($field) {
            return true;
        });
        $f->addValidatorFunction('false', function ($field) {
            return 'error message';
        });
        $this->assertFalse($f->validate());
        $this->assertFalse($f->validated());
        $this->assertEquals(['testfield'=>'error message'], $f->validationMessage());
        //removing validator
        $f = new Container('test field');
        $f->addValidatorFunction('true', function ($field) {
            return true;
        });
        $f->addValidatorFunction('false', function ($field) {
            return 'error message';
        });
        $f->removeValidator('false');
        $this->assertTrue($f->validate());
        $this->assertTrue($f->validated());
    }

    public function testNestedValidation()
    {
        //the following use one container c and one nested field f
        //case 1: c:valid, f:valid: valid
        $c = new Container('Container');
        $c['f'] = new Input('Field');
        $this->assertTrue($c->validate());
        $this->assertTrue($c->validated());
        $this->assertTrue($c['f']->validate());
        $this->assertTrue($c['f']->validated());
        //case 2: c:valid, f:invalid: invalid
        $c = new Container('Container');
        $c['f'] = new Input('Field');
        $c['f']->addValidator('false', new SimpleValidationHarness(false));
        $this->assertFalse($c->validate());
        $this->assertFalse($c->validated());
        $this->assertFalse($c['f']->validate());
        $this->assertFalse($c['f']->validated());
        //case 3: c:invalid, f:valid: invalid
        $c = new Container('Container');
        $c['f'] = new Input('Field');
        $c->addValidator('false', new SimpleValidationHarness(false));
        $this->assertFalse($c->validate());
        $this->assertFalse($c->validated());
        $this->assertTrue($c['f']->validate());
        $this->assertTrue($c['f']->validated());
        //case 4: c:invalid, f:invalid: invalid
        $c = new Container('Container');
        $c['f'] = new Input('Field');
        $c->addValidator('false', new SimpleValidationHarness(false));
        $c['f']->addValidator('false', new SimpleValidationHarness(false));
        $this->assertFalse($c->validate());
        $this->assertFalse($c->validated());
        $this->assertFalse($c['f']->validate());
        $this->assertFalse($c['f']->validated());
    }
}

class SimpleValidationHarness extends AbstractValidator
{
    protected $value;
    public function __construct(bool $value)
    {
        $this->value = $value;
    }
    public function validate() : bool
    {
        return $this->value;
    }
    public function message() : ?string
    {
        return 'simple validator message';
    }
}
