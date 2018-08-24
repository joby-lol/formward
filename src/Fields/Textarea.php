<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;

class Textarea extends AbstractField
{
    public $tag = 'textarea';
    public $selfClosing = false;

    protected function htmlTagContent() : ?string
    {
        return htmlentities($this->value());
    }
}
