<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;

class Button extends AbstractField
{
    protected $classes = array('Button');

    public function wrapperContentOrder() : array
    {
        return array(
            '{field}'
        );
    }

    protected function htmlTagContent()
    {
        return $this->label();
    }

    protected function htmlTag()
    {
        return 'button';
    }

    protected function htmlTagSelfClosing()
    {
        return false;
    }
}
