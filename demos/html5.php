<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

//These are the basic containers
//This example uses AjaxForm simply for conveniently loading the core CSS
use Digraph\Forms\AjaxForm;
use Digraph\Forms\Fields\FieldSet;

//Most modern input types you want are probably in Digraph\Forms\Fields\HTML5
use Digraph\Forms\Fields\HTML5\Field;
use Digraph\Forms\Fields\HTML5\Password;
use Digraph\Forms\Fields\HTML5\PasswordConfirmed;
use Digraph\Forms\Fields\HTML5\Date;
use Digraph\Forms\Fields\HTML5\Time;
use Digraph\Forms\Fields\HTML5\DateTime;
use Digraph\Forms\Fields\HTML5\Checkbox;
use Digraph\Forms\Fields\HTML5\Radio;
use Digraph\Forms\Fields\HTML5\CheckboxMulti;
use Digraph\Forms\Fields\HTML5\RadioMulti;
use Digraph\Forms\Fields\HTML5\Color;
use Digraph\Forms\Fields\HTML5\Email;
use Digraph\Forms\Fields\HTML5\Number;
use Digraph\Forms\Fields\HTML5\Range;
use Digraph\Forms\Fields\HTML5\URL;

//basic text input
$form = new AjaxForm('HTML5 Field Types');
$form['basic'] = new Field('Basic HTML5 Field');
$form['basic_disabled'] = new Field('Disabled Basic HTML5 Field');
$form['basic_disabled']->disabled();

//passwords
$form['pw'] = new FieldSet('Passwords');
$form['pw'][] = new Password('Password input');
$form['pw'][] = new PasswordConfirmed('Password input with built-in confirmation');

//dates and times
$form['dt'] = new FieldSet('Dates and Times');
$form['dt'][] = new Date('Date');
$form['dt'][] = new Time('Time');
$form['dt'][] = new DateTime('DateTime combo field');

//checkboxes, radio buttons, dropdowns, other selection types
$form['ms'] = new FieldSet('Checkboxes, Radio Buttons, MultiSelects');
$form['ms'][] = new Checkbox('Stand-alone checkbox');
$form['ms'][] = new Radio('Stand-alone radio button (useless, I know)');
$form['ms']['cbms'] = new CheckboxMulti('Checkbox MultiSelect');
$form['ms']['cbms']->setOptions(array(
    'a' => 'Option A',
    'b' => 'Option B',
    'c' => 'Option C'
));
$form['ms']['cbms']->required();
$form['ms']['rdms'] = new RadioMulti('Radio MultiSelect');
$form['ms']['rdms']->setOptions(array(
    'a' => 'Option A',
    'b' => 'Option B',
    'c' => 'Option C'
));

//miscellaneous other types
$form['ot'] = new FieldSet('Other types defined in HTML5');
$form['ot'][] = new Color('Color');
$form['ot'][] = new Email('Email');
$form['ot'][] = new Number('Number');
$form['ot'][] = new Range('Range');
$form['ot'][] = new URL('URL');

//calling handle() handles resource requests and performs validation on submit
$result = $form->handle(
    function ($form) {
        return 'Form accepted';
    },
    function ($form) {
        return 'Form has a validation error';
    },
    function ($form) {
        return 'Form has not been submitted';
    }
);

?><!doctype html>
<html>
    <head>
        <?php echo AjaxForm::loadResourcesHTML(true); ?>
    </head>
<body>
<div style="max-width:25em;margin:0 auto;">

    <?php echo $form; ?>

    <h2>$form->handle() returned</h2>
    <?php echo $result; ?>

</div>
</body>
</html>
