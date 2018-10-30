<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;

/**
 * An input class that doesn't actually take any input, instead it uses its
 * content() getter/setter method to let you display HTML content of your
 * own creation, mid-form and wrapped in a FieldWrapper
 *
 * Cannot have values, and always validates successfully, and is also never
 * required or disabled.
 */
class DisplayOnly extends AbstractField
{
    public $tag = 'div';
    public $selfClosing = false;
    protected $displayContent = '';
    protected $required = false;
    protected $disabled = false;

    public function content(string $set = null) : string
    {
        if ($set !== null) {
            $this->displayContent = $set;
        }
        return $this->displayContent;
    }

    public function required($required = null)
    {
        return false;
    }

    public function disabled($disabled = null)
    {
        return false;
    }

    public function validate() : bool
    {
        return true;
    }

    public function value($set = null)
    {
        return null;
    }

    public function default($set = null)
    {
        return null;
    }

    public function submittedValue($set = null)
    {
        return null;
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
        return $this->content();
    }
}
