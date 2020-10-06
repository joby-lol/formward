<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\AjaxFields;

use Formward\FieldInterface;
use Formward\Fields\Container;
use Formward\Fields\DisplayOnly;
use Formward\Fields\Input;
use Formward\Fields\Select;

class AjaxAutocomplete extends Container
{
    protected $ajaxSource;
    protected $ajaxQueryHandler;

    public function construct()
    {
        /*
        Set up sub-fields

        query: The search field for querys from the user
        results: only used by front-end, to display ajax results
        value: used for storing the value actually submitted -- must be
            validated somehow
         */
        // $this->wrapContainerItems(false);
        $this['query'] = new Input('');
        $this['query']->addClass('ajax-field-query');
        $this['query']->attr('autocomplete', 'off');
        $this['value'] = new Select('Results');
        $this['value']->nullText = '-- select --';
        $this['value']->addClass('ajax-field-value');
        $this['results'] = new DisplayOnly('');
        $this['results']->addClass('ajax-field-results');
        $this->addClass('FormwardAjaxAutocomplete');
        $this->ajaxPrevalidation();
        /*
        Validation works by verifying the contents of value using this
        field's validateAjaxValue() method. The default validateAjaxValue()
        method will look up the string in query and verify that the value
        in value is returned from that search.
         */
        $this->addValidatorFunction(
            'ajaxValidator',
            function (&$field) {
                $field->ajaxPrevalidation();
                //validate ajax value
                if ($field['query']->value()) {
                    return $this->ajaxValidateValue();
                }
                //required
                if ($field->required()) {
                    if ($field->value() === null) {
                        return 'Please select an item';
                    }
                }
                return true;
            }
        );
    }

    public function string() : string
    {
        $this->ajaxPrevalidation();
        return parent::string();
    }

    /*
    required doesn't recurse
     */
    public function required($set = null, $clientSide = true)
    {
        if ($set !== null) {
            if ($set) {
                $this->addClass('required');
            } else {
                $this->removeClass('required');
            }
            $this->required = $set;
            $this->clientSideRequired = $clientSide;
        }
        return $this->required;
    }

    /*
    value() passes through to value
     */
    public function value($value = null)
    {
        return $this['value']->value($value);
    }

    /*
    default() passes through to value
     */
    public function default($value = null)
    {
        $this['query']->default($value);
        return $this['value']->default($value);
    }

    public function ajaxValidateValue()
    {
        $value = $this['value']->value();
        $query = $this['query']->value();
        if (!($results = $this->ajaxGetResults($query))) {
            return 'No matches for the query "'.htmlentities($query).'"';
        }
        if (!isset($results[$value])) {
            return 'Please select a result from the displayed options';
        }
        return true;
    }

    /*
    Loads results from a callable query handler that can be passed in via
    ajaxQueryHandler() -- query handlers must accept a single argument, for the
    string being queried.
     */
    public function ajaxGetResults($query)
    {
        if ($this->ajaxQueryHandler()) {
            return call_user_func_array($callback, [$query]);
        }
        if ($result = $this->ajaxUrl($query)) {
            if ($result = file_get_contents($result)) {
                if ($result = json_decode($result, true)) {
                    return $result;
                }
            }
        }
        return [];
    }

    /*
    Set up fields before validation step begins, this gets run on construction,
    then again before validation or conversion to a string

    The default implementation converts ajax results into a Select field
     */
    public function ajaxPrevalidation()
    {
        if (!$this['query']) {
            return;
        }
        if ($options = $this->ajaxGetResults($this['query']->value())) {
            $this['value']->removeClass('hidden');
            $this['value']->options(array_map(
                function ($e) {
                    if (is_array($e)) {
                        return $e['text'];
                    }
                    return $e;
                },
                $options
            ));
        } else {
            $this['value']->addClass('hidden');
        }
    }

    public function ajaxUrl(string $query = null)
    {
        if ($this->ajaxSource()) {
            return str_replace('$q', urlencode($query), $this->ajaxSource());
        }
        return null;
    }

    public function ajaxSource(string $ajaxSource = null)
    {
        if ($ajaxSource !== null) {
            $this->ajaxSource = $ajaxSource;
            $this->attr('data-ajaxsource', base64_encode($ajaxSource));
        }
        return $this->ajaxSource;
    }

    public function ajaxQueryHandler($ajaxQueryHandler = null)
    {
        if ($ajaxQueryHandler !== null) {
            $this->ajaxQueryHandler = $ajaxQueryHandler;
        }
        return $this->ajaxQueryHandler;
    }
}
