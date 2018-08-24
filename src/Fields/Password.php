<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class Password extends Input
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->type('password');
    }

    /**
     * Passwords don't get their value put back out in HTML
     */
    protected function htmlAttributes()
    {
        $attr = parent::htmlAttributes();
        unset($attr['value']);
        return $attr;
    }
}
