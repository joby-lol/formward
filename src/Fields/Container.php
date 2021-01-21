<?php
/* Formward | https://github.com/jobyone/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractContainer;

class Container extends AbstractContainer
{
    /**
     * get/set whether this field is disabled
     */
    public function disabled($set = null)
    {
        if ($set !== null) {
            $this->recursiveSet('disabled', $set);
        }
        return parent::disabled($set);
    }
}
