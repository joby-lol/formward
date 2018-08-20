<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\SystemFields;

use Formward\AbstractField;

class SystemButton extends AbstractField
{
    protected $classes = array('Button','SystemButton');

    public function wrapperContentOrder() : array
    {
        return array(
            '{field}'
        );
    }

    /**
     * prefix all system field names with an _ to prevent collisions with
     * user-space fields
     */
    public function name(string $name = null)
    {
        return '_'.parent::name($name);
    }

    /**
     * Buttons don't get names
     */
    protected function fieldAttributes()
    {
        $out = parent::fieldAttributes();
        unset($out['name']);
        return $out;
    }

    /**
     * Buttons get their labels wrapped in the button tag, not as an attribute
     */
    protected function htmlTagContent() : ?string
    {
        return $this->label();
    }

    /**
     * button tag
     */
    protected function htmlTag()
    {
        return 'button';
    }

    /**
     * Not self-closing
     */
    protected function htmlTagSelfClosing()
    {
        return false;
    }
}
