<?php @session_start() ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title></title>
        <link rel="stylesheet" href="basic-styles.css">
    </head>
    <body>
<?php

include '../vendor/autoload.php';

$form = new Formward\Form('Test form');
$form['test'] = new Formward\Fields\Input('Test field');
$form['test']->required(true);

/*
Converting a form to a string will check its submission state and validate it if
it is submitted. It will not call any of the callbacks. You have to call
handle() to get those to run.

This means you can echo and handle the form in any order you like. It's probably
marginally faster to handle it first, but it's also probably not worth worrying
about the difference.
 */
echo $form;

echo "<h2>Form state</h2>";
$result = $form->handle();
if ($result === true) {
    echo "<p>Submitted and valid</p>";
} elseif ($result === false) {
    echo "<p>Submitted and invalid</p>";
} elseif ($result === null) {
    echo "<p>Not submitted</p>";
}

echo "<h2>Form::value()</h2>";
echo "<pre>";
print_r($form->value());
echo "</pre>";

?>
    </body>
</html>
