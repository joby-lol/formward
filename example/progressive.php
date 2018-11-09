<?php
/**
 * This file demonstrates the classes that can be progressively enhanced to
 * provide a fancier user experience
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
        <script src="basic-pe.js"></script>
    </head>
    <body>
<?php

// create a new form
$form = new Formward\Form('Demo form');
// $form->method('get');

//date/time
$form['ordering'] = new Formward\Fields\Ordering('\\Formward\\Fields\\Ordering');
$form['ordering']->opts([
    'a' => 'Option A',
    'b' => 'Option B',
    'c' => 'Option C',
    'd' => 'Option D'
]);

$form['ordering2'] = new Formward\Fields\Ordering('\\Formward\\Fields\\Ordering');
$form['ordering2']->opts([
    'a' => 'Option A',
    'b' => 'Option B',
    'c' => 'Option C',
    'd' => 'Option D'
]);
$form['ordering2']->allowDeletion(true);
$form['ordering2']->allowAddition(true);
$form['ordering2']->required(true);
$form['ordering2']->addTip('This one has the optional flags set to allow both deleting and adding custom values');

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
