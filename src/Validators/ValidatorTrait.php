<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Validators;

use Digraph\Utilities\ValueFunctionTrait;

trait ValidatorTrait
{
    private $validators = array();

    /**
     * run validation functions and objects to determine whether to return true
     * or false
     */
    public function validate() : bool
    {
        if ($this->validated() !== null) {
            return $this->validated();
        }
        //run validation checks, save the first message to come up and return false
        foreach ($this->validators as $v) {
            if ($v instanceof ValidatorInterface) {
                //use validator object
                if (!$v->validate()) {
                    $this->validationMessage($v->message());
                    return $this->validated(false);
                }
            } else {
                //use validator function
                if (($message = $v($this)) !== true) {
                    $this->validationMessage($message);
                    return $this->validated(false);
                }
            }
        }
        //by default assume we are validated
        return $this->validated(true);
    }

    public function validated(bool $set = null) : ?bool
    {
        return static::valueFunction('validated', $set);
    }

    public function validationMessage(string $set = null)
    {
        return static::valueFunction('validationMessage', $set);
    }

    public function addValidator(string $name, ValidatorInterface $validator)
    {
        $this->validators[$name] = $validator;
    }

    public function addValidatorFunction(string $name, callable $validator)
    {
        $this->validators[$name] = $validator;
    }

    public function removeValidator(string $name)
    {
        unset($this->validators[$name]);
    }
}
