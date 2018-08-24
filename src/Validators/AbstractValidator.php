<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Validators;

use Formward\FieldInterface;

abstract class AbstractValidator implements ValidatorInterface
{
    protected $field;
    protected $message;

    public function field(FieldInterface &$set = null) : ?FieldInterface
    {
        if ($set !== null) {
            $this->field = $set;
        }
        return $this->field;
    }

    public function message() : ?string
    {
        return $this->message;
    }
}
