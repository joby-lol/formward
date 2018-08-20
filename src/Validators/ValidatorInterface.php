<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Validators;

use Formward\FieldInterface;

interface ValidatorInterface
{
    public function field(FieldInterface &$set = null) : ?FieldInterface;
    public function validate();
    public function message();
}
