<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\SystemFields;

use Formward\AbstractField;
use Formward\FieldInterface;
use Formward\Fields\Button;

class SystemButton extends Button
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->addClass('SystemButton');
    }

    /**
     * prefix all system field names with an _ to prevent collisions with
     * user-space fields
     */
    public function name($name = null)
    {
        return '_'.parent::name($name);
    }
}
