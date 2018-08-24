<?php
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

// add a simple input field and mark it as required
$form['test'] = new Formward\Fields\Input('Test field');
$form['test']->required(true);

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
