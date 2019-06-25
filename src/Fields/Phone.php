<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class Phone extends Input
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        //add a validator to ensure emails are valid
        //only validates if value isn't empty, because required-ness is a separate concern
        $this->addValidatorFunction(
            'email',
            function (&$field) {
                if ($field->value() == '' || preg_match("/^((\+[0-9]{1,3} )?\([0-9]{3}\) )?[0-9]{3}\-[0-9]{4}$/i", $field->value())) {
                    return true;
                }
                return "Valid phone numbers must be: 7 digits, 10 digits, or 10 digits plus a country code.";
            }
        );
        // add type=email to markup
        $this->type('tel');
    }

    protected function cleanup($value)
    {
        $value = preg_replace('/[^0-9]/', '', $value);
        if (strlen($value) == 7) {
            $value = preg_replace('/^([0-9]{3})([0-9]{4})$/', '$1-$2', $value);
        } elseif (strlen($value) == 10) {
            $value = preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{4})$/', '($1) $2-$3', $value);
        } else {
            $value = preg_replace('/^([0-9]{1,3})([0-9]{3})([0-9]{3})([0-9]{4})$/', '+$1 ($2) $3-$4', $value);
        }
        return $value;
    }

    public function value($set=null)
    {
        return $this->cleanup(parent::value($set));
    }

    public function default($set=null)
    {
        return $this->cleanup(parent::default($set));
    }
}
