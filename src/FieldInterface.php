<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward;

interface FieldInterface
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null);
    public function &parent(FieldInterface &$parent=null) : ?FieldInterface;
    public function attr(string $name, string $value = null) : ?string;
    public function submittedValue();
    public function value($value = null);
    public function default($default = null);
    public function classes(string $prefix = null) : array;
    public function addClass(string $class);
    public function removeClass(string $class);

    public function required($required = null);
    public function validate() : bool;
    public function validated() : ?bool;
    public function validationMessage();

    public function addValidator(string $name, Validators\ValidatorInterface $validator);
    public function addValidatorFunction(string $name, callable $validator);
    public function removeValidator(string $name);

    public function containerMayWrap() : bool;
    public function wrapperContentOrder() : array;

    public function __toString();
}
