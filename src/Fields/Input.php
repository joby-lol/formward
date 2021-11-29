<?php
/* Formward | https://github.com/jobyone/formward | MIT License */

namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;

class Input extends AbstractField
{
    public $tag = 'input';
    public $selfClosing = true;

    public function __construct(string $label, string $name = null, FieldInterface $parent = null)
    {
        parent::__construct($label, $name, $parent);
        $this->attr('type', 'text');
    }

    protected function htmlAttributes()
    {
        $attr = parent::htmlAttributes();
        if (method_exists($this, 'htmlValue')) {
            if (($value = $this->htmlValue()) !== null) {
                $attr['value'] = $value;
            }
        } elseif ($value = $this->value()) {
            $attr['value'] = $value;
        }
        if ($this->required && $this->clientSideRequired) {
            $attr['required'] = 'true';
        }
        if ($this->disabled()) {
            $attr['disabled'] = true;
        }
        return $attr;
    }

    public function value($set = null)
    {
        $value = parent::value($set);
        if (is_string($value)) {
            return trim($value, ' \t\0\x0B');
        }
        return $value;
    }

    public function type(string $type = null)
    {
        return $this->attr('type', $type);
    }
}
