<?php
/* Formward | https://github.com/jobyone/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class Number extends Input
{
    public function __construct(string $label, string $name = null, FieldInterface $parent = null)
    {
        parent::__construct($label, $name, $parent);
        $this->attr('type', 'number');
    }

    public function htmlValue()
    {
        var_dump($this->value());
        if ($this->value() === '0' || $this->value() === 0) {
            return '0';
        } else {
            return $this->value();
        }
    }
}
