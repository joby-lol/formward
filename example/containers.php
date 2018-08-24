<?php
/**
 * This file demonstrates the use of Container fields, which are a special sort
 * of container that can hold other fields inside them.
 */
include '../vendor/autoload.php';
@session_start();
 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title></title>
        <link rel="stylesheet" href="basic-styles.css">
    </head>
    <body>
<?php

// create a new form
$form = new Formward\Form('Test form');

// add the first Container with two Inputs in it
$form['first'] = new Formward\Fields\Container('First container');
$form['first']['first'] = new Formward\Fields\Input('First input');
$form['first']['second'] = new Formward\Fields\Input('Second input');

// add the second Container with two Inputs in it
$form['second'] = new Formward\Fields\Container('Second container');
$form['second']['first'] = new Formward\Fields\Input('First input');
$form['second']['second'] = new Formward\Fields\Input('Second input');

// calling required() on $form['second'] will propagate down into child fields
$form['second']->required(true);

// output the form to the page
echo $form;

// calling $form->handle() returns one of true, false, or null, which mean:
// true:  form submitted and validated
// false: form submitted but has validation errors
// null:  form not submitted
$result = $form->handle();

echo "<h2>Form state</h2>";
if ($result === true) {
    echo "<p>Submitted and valid</p>";
} elseif ($result === false) {
    echo "<p>Submitted and invalid</p>";
} elseif ($result === null) {
    echo "<p>Not submitted</p>";
}

// an array of all current form values can be gotten from $form->value()
echo "<h2>Form::value()</h2>";
echo "<pre>";
print_r($form->value());
echo "</pre>";

?>
    </body>
</html>
