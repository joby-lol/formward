<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

use Digraph\Forms\AjaxForm;
use Digraph\Forms\Fields\FieldSet;

use Digraph\Forms\Fields\Files\SimpleFile;
use Digraph\Forms\Fields\Files\MultiFile;

$form = new AjaxForm('File handling');

$form['simplefile'] = new SimpleFile('SimpleFile - single vanilla file');
$form['simplefile']->size(1024*1024);

$form['multifile'] = new MultiFile('MultiFile - multiple vanilla file');
$form['multifile']->size(1024*1024);

$result = $form->handle();

?><!doctype html>
<html>
    <head>
        <?php echo AjaxForm::loadResourcesHTML(true); ?>
    </head>
<body>
<div style="max-width:25em;margin:0 auto;">

<?php echo $form; ?>

</div>

<?php
if ($result) {
    echo "<h2>Form submitted</h2>";
    echo "<h3>SimpleFile value</h3>";
    var_dump($form['simplefile']->getValue());
    echo "<h3>MultiFile value</h3>";
    var_dump($form['multifile']->getValue());
}
?>

<?php echo AjaxForm::initResourcesHTML(true); ?>
</body>
</html>
