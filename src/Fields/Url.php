<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class Url extends Input
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        //add a validator to ensure emails are valid
        //only validates if value isn't empty, because required-ness is a separate concern
        $this->addValidatorFunction(
            'url',
            function (&$field) {
                if ($field->value() == '' || filter_var($field->value(), FILTER_VALIDATE_URL)) {
                    return true;
                }
                return "Enter a valid URL";
            }
        );
        // add type=email to markup
        $this->type('url');
    }
}
