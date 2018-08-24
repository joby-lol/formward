<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Validators;

trait ValidatorTrait
{
    protected $validators = [];
    protected $validated = null;
    protected $validationMessage = null;

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

    public function validated($set = null) : ?bool
    {
        if ($set !== null) {
            $this->validated = $set;
        }
        return $this->validated;
    }

    public function validationMessage($set = null)
    {
        if ($set !== null) {
            $this->validationMessage = $set;
        }
        return $this->validationMessage;
    }

    public function addValidator(string $name, ValidatorInterface $validator)
    {
        $validator->field($this);
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
