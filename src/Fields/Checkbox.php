<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class Checkbox extends Input
{
    protected $value = null;

    public function __construct(string $label, string $name = null, FieldInterface $parent = null)
    {
        parent::__construct($label, $name, $parent);
        $this->type('checkbox');
        $this->default(false);
    }

    public function value($value = null)
    {
        if ($this->value === null) {
            return parent::value() == 'on';
        } else {
            return $this->value = $value;
        }
    }

    /**
     * Checkboxes have their label after their input tag
     */
    public function wrapperContentOrder(): array
    {
        return array(
            '{field}',
            '{label}',
            '{tips}',
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
