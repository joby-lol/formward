<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\SystemFields;

use Formward\FieldInterface;

class Submit extends SystemButton
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->addClass('Submit');
    }

    protected function fieldAttributes()
    {
        $out = parent::fieldAttributes();
        $out['type'] = 'submit';
        return $out;
    }
}
