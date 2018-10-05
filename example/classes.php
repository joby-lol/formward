<?php
/**
 * This file demonstrates the basic classes that come in Formward
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
$form = new Formward\Form('Demo form');

// add a simple input field and mark it as required
$form['checkbox1'] = new Formward\Fields\Checkbox('Formward\\Fields\\Checkbox');
$form['checkbox1']->default(false);
$form['checkbox2'] = new Formward\Fields\Checkbox('Formward\\Fields\\Checkbox');
$form['checkbox2']->default(true);
$form['checkbox2']->addTip('This one is on by default, but should still know when it\'s unchecked');

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
