<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;

class Input extends AbstractField
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->attr('type', 'text');
    }

    public function type(string $type = null)
    {
        return $this->attr('type', $type);
    }

    /**
     * Return the attributes that a field should have. This function may need
     * overriding in some cases.
     */
    protected function fieldAttributes()
    {
        $out = parent::fieldAttributes();
        $out['value'] = $this->value();
        if ($this->required()) {
            $out['required'] = 'true';
        }
        return $out;
    }

    protected function htmlTag()
    {
        return 'input';
    }

    protected function htmlTagSelfClosing()
    {
        return true;
    }
}
