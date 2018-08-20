<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
declare(strict_types=1);
namespace Formward;

use PHPUnit\Framework\TestCase;

class AbstractFieldTest extends TestCase
{
    use \SteveGrunwell\PHPUnit_Markup_Assertions\MarkupAssertionsTrait;

    public function testValues()
    {
        $f = new AbstractFieldHarness('Test field', 'test');
        //check name
        $this->assertEquals('test', $f->name());
        //check values
        $this->assertNull($f->default());
        $this->assertNull($f->submittedValue());
        $this->assertNull($f->value());
        //check POST should be default
        $_POST['test'] = 'value';
        $this->assertNull($f->default());
        $this->assertEquals('value', $f->value());
        $this->assertEquals('value', $f->submittedValue());
        //values in GET mode should be null
        $f->method('get');
        $this->assertNull($f->default());
        $this->assertNull($f->submittedValue());
        $this->assertNull($f->value());
        //unset POST and set GET
        $_POST = array();
        $_GET['test'] = 'value';
        $this->assertNull($f->default());
        $this->assertEquals('value', $f->value());
        $this->assertEquals('value', $f->submittedValue());
        //values in POST mode should be null
        $f->method('post');
        $this->assertNull($f->default());
        $this->assertNull($f->submittedValue());
        $this->assertNull($f->value());
    }

    public function testDefault()
    {
        $_GET = $_POST = array();
        $f = new AbstractFieldHarness('Test field', 'test');
        $f->default('default value');
        //test with nothing submitted
        $this->assertNull($f->submittedValue());
        $this->assertEquals('default value', $f->default());
        $this->assertEquals('default value', $f->value());
        //with something submitted it should override the default
        $_POST['test'] = 'submitted value';
        $this->assertEquals('submitted value', $f->submittedValue());
        $this->assertEquals('default value', $f->default());
        $this->assertEquals('submitted value', $f->value());
    }

    public function testValidatorObjects()
    {
        //simple validator object that is always true
        $f = new AbstractFieldHarness('test field');
        $f->addValidator('true', new SimpleValidationHarness(true));
        $this->assertTrue($f->validate());
        $this->assertTrue($f->validated());
        $this->assertNull($f->validationMessage());
        //simple validator object that is always false
        $f = new AbstractFieldHarness('test field');
        $f->addValidator('true', new SimpleValidationHarness(true));
        $f->addValidator('false', new SimpleValidationHarness(false));
        $this->assertFalse($f->validate());
        $this->assertFalse($f->validated());
        $this->assertEquals('simple validator message', $f->validationMessage());
        //removing validator
        $f = new AbstractFieldHarness('test field');
        $f->addValidator('true', new SimpleValidationHarness(true));
        $f->addValidator('false', new SimpleValidationHarness(false));
        $f->removeValidator('false');
        $this->assertTrue($f->validate());
        $this->assertTrue($f->validated());
    }

    public function testValidatorFunctions()
    {
        //simple validator object that is always true
        $f = new AbstractFieldHarness('test field');
        $f->addValidatorFunction('true', function ($field) {
            return true;
        });
        $this->assertTrue($f->validate());
        $this->assertTrue($f->validated());
        $this->assertNull($f->validationMessage());
        //simple validator object that is always false
        $f = new AbstractFieldHarness('test field');
        $f->addValidatorFunction('true', function ($field) {
            return true;
        });
        $f->addValidatorFunction('false', function ($field) {
            return 'error message';
        });
        $this->assertFalse($f->validate());
        $this->assertFalse($f->validated());
        $this->assertEquals('error message', $f->validationMessage());
        //removing validator
        $f = new AbstractFieldHarness('test field');
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
}

class SimpleValidationHarness extends Validators\AbstractValidator
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

class AbstractFieldHarness extends AbstractField
{
    protected function htmlTag()
    {
        return 'input';
    }

    protected function htmlTagSelfClosing()
    {
        return true;
    }
}
