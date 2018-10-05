<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;
use Formward\FormInterface;

class Checkbox extends Input
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->type('checkbox');
        $this->default(false);
    }

    public function submittedValue()
    {
        //locate root parent
        if (($form = $this->rootParent()) && ($form instanceof FormInterface)) {
            //verify that root is a Form
            if ($form instanceof FormInterface) {
                //check if form is submitted
                if ($form->submitted()) {
                    return parent::submittedValue() == 'on';
                }
            }
        }
        //if we don't have a root Form or if it isn't submitted then value is unknown
        return null;
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
