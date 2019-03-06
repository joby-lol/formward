<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

use Digraph\Forms\AjaxForm;
use Digraph\Forms\Fields\FieldSet;
use Digraph\Forms\Fields\Ajax\AbstractAutocomplete;
use Digraph\Forms\Fields\Ajax\AjaxDateTime;
use Digraph\Forms\Fields\Ajax\AjaxDate;

include __DIR__ . '/_ajax/examples.php';

//Instantiate a new AjaxForm and add a field to it
$form = new AjaxForm('A basic Ajax Form');

$form['datetime'] = new AjaxDateTime('Ajax DateTime');
$form['date'] = new AjaxDate('Ajax Date');

$form['autocomplete'] = new FieldSet('Autocomplete Fields');
$form['autocomplete']->addTip('Created by extending an abstract autocomplete class, see _ajax/examples.php');

$form['autocomplete']['names'] = new NamesField('Names Autocomplete Field');
$form['autocomplete']['names']->limitToCompletions();

$form['autocomplete']['names_disabled'] = new NamesField('Disabled Names Autocomplete Field');
$form['autocomplete']['names_disabled']->disabled();

$form['autocomplete']['primes'] = new PrimeFactorsField('Prime Factors Autocomplete Field');
$form['autocomplete']['primes']->limitToCompletions();

//Register internal scripts. Generally you should not specify an index using the
//second argument when doing this. That way the scripts can be deduplicated, and
//only run once even if registered by multiple forms on the same page.
$form->registerInternalLoadScript('console.log("we have a load script!")');
$form->registerInternalInitScript('console.log("we have an init script!")');

//Handle the form using the simple helper function. Can be passed callbacks or
//return values for accepted, invalid, and not submitted states.
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
        <?php
        /*
        The easiest way to get all the resources for the page is by echoing
        AjaxForm::resourcesHTML(true) in the head. This will link all external
        resources, and bundle all the internal resources into one JS file
        and one CSS file.

        In the future, this may even inline the internal CSS/JS if the filesize
        is small enough.

        When bundled, each script resource is automatically wrapped in its own
        closure to avoid scope problems.

        When called this way, init scripts are automatically wrapped so that
        they don't run until the DOM is ready.
         */
        echo AjaxForm::resourcesHTML(true);
        ?>
    </head>
<body>
<div style="max-width:25em;margin:0 auto;">

<?php
//To display the form, simply echo it
echo $form;
?>

<h2>$form->handle() returned</h2>
<?php echo $result; ?>

</div>
</body>
</html>
