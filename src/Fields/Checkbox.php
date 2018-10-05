<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class Checkbox extends Input
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->type('checkbox');
        $this->default(false);
    }

    /**
     * Convert the underlying 'on' values to boolean
     */
    public function value($set = null)
    {
        if ($set !== null) {
            parent::value($set?'on':'');
        }
        return parent::value() == 'on';
    }

    /**
     * Convert the underlying 'on' values to boolean
     */
    public function default($set = null)
    {
        if ($set !== null) {
            parent::default($set?'on':'');
        }
        return parent::default() == 'on';
    }

    /**
     * Checkboxes have their label after their input tag
     */
    public function wrapperContentOrder() : array
    {
        return array(
            '{field}',
            '{label}',
            '{tips}'
        );
    }

    /**
     * Checkboxes don't get their value put back out in HTML
     */
    protected function htmlAttributes()
    {
        $attr = parent::htmlAttributes();
        unset($attr['value']);
        if ($this->value()) {
            $attr['checked'] = true;
        }
        return $attr;
    }
}
