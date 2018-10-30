<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;

class Textarea extends AbstractTransformedInput
{
    public $tag = 'textarea';
    public $selfClosing = false;

    protected function transformValue($value)
    {
        return $value;
    }

    protected function unTransformValue($value)
    {
        return $value;
    }

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
        if (method_exists($this, 'htmlValue')) {
            $value = $this->htmlValue();
        } else {
            $value = $this->value();
        }
        if (!$value) {
            $value = '';
        }
        return htmlentities($value);
    }
}
