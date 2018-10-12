<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;

class Textarea extends AbstractField
{
    public $tag = 'textarea';
    public $selfClosing = false;

    protected function htmlAttributes()
    {
        $attr = parent::htmlAttributes();
        if ($this->required()) {
            $attr['required'] = 'true';
        }
        if ($this->disabled()) {
            $attr['disabled'] = true;
        }
        return $attr;
    }

    protected function htmlContent() : ?string
    {
        return htmlentities($this->value());
    }
}
