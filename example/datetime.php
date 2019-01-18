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
// $form->method('get');

//Date fields return a string in the format Y-m-d, and can be set with a string
//parseable by strtotime, or by a timestamp
$form['date'] = new Formward\Fields\Date('\\Formward\\Fields\\Date');
$form['date']->default('2018-12-25');
//Time fields are the same, but return a string in the format H:i
$form['time'] = new Formward\Fields\Time('\\Formward\\Fields\\Time');
$form['time']->default(time());
//DateAndTime fields return a timestamp, and can be set with a string or timestamp
$form['datetime'] = new Formward\Fields\DateAndTime('\\Formward\\Fields\\DateAndTime');
$form['datetime']->default(time());

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

// display POST value
echo "<h2>_POST</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

?>
    </body>
</html>
