<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;

class Button extends AbstractField
{
    public $tag = 'button';
    public $selfClosing = false;

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->addClass('Button');
    }

    public function wrapperContentOrder() : array
    {
        return array(
            '{field}'
        );
    }

    protected function htmlContent()
    {
        return $this->label();
    }
}
