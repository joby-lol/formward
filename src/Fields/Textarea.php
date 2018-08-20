<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;

class Textarea extends AbstractField
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->attr('type', 'text');
    }

    protected function htmlTagContent() : ?string
    {
        return htmlentities($this->value());
    }

    protected function htmlTag()
    {
        return 'textarea';
    }

    protected function htmlTagSelfClosing()
    {
        return false;
    }
}
