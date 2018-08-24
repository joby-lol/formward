<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;

class Input extends AbstractField
{
    public $tag = 'input';
    public $selfClosing = true;

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->attr('type', 'text');
    }

    protected function htmlAttributes()
    {
        $attr = parent::htmlAttributes();
        if ($value = $this->value()) {
            $attr['value'] = $value;
        }
        if ($this->required()) {
            $attr['required'] = 'true';
        }
        return $attr;
    }

    public function value($set = null)
    {
        return trim(parent::value($set));
    }

    public function type(string $type = null)
    {
        return $this->attr('type', $type);
    }
}
