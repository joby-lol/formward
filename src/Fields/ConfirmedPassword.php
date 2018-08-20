<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class ConfirmedPassword extends Container
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this['password'] = new Password('Enter password');
        $this['confirmpassword'] = new Password('Confirm password');
        $this->addValidatorFunction(
            'matching',
            function (&$field) {
                if ($field['password']->value() != $field['confirmpassword']->value()) {
                    return 'Passwords must match';
                }
                return true;
            }
        );
    }

    public function value($value = null)
    {
        return $this['password']->value($value);
    }

    public function matchingValidator()
    {
        var_dump(func_get_args());
        exit();
    }
}
