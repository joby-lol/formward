<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractContainer;

class Container extends AbstractContainer
{
    /**
     * get/set whether this field is required
     */
    public function required($set = null)
    {
        if ($set !== null) {
            $this->recursiveSet('required', $set);
        }
        return parent::required($set);
    }
}
