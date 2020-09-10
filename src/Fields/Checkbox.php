<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;
use Formward\FormInterface;

class Checkbox extends Input
{
    protected $value = null;

    public function __construct(string $label, string $name = null, FieldInterface $parent = null)
    {
        parent::__construct($label, $name, $parent);
        $this->type('checkbox');
        $this->default(false);
    }

    public function submittedValue()
    {
        if (($form = $this->rootParent()) && ($form instanceof FormInterface)) {
            // we have a root form, so we can check if it's submitted, and
            // get a proper true/false/null result
            if ($form->submitted()) {
                return parent::submittedValue() == 'on';
            }else {
                return null;
            }
        }else {
            // there is no root form, so the result will always be true/false
            return parent::submittedValue() == 'on';
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
