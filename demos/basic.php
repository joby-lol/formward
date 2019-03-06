<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

use Digraph\Forms\Fields\FieldSet;
use Digraph\Forms\Form;
use Digraph\Forms\Fields\Ajax\AbstractAjaxField;

use Digraph\Forms\Fields\HTML5\Field;

$form = new Form('A form to do a thing');
$form['field1'] = new Field('First Field');
$form['field2'] = new Field('Second Field');

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
        
    </head>
<body>
<div style="max-width:25em;margin:0 auto;">

<?php echo $form; ?>

<h2>$form->handle() returned</h2>
<?php echo $result; ?>

</div>
</body>
</html>
