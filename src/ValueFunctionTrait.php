<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward;

/**
 * Utility for creating encapsulated getter/setter functions that can be used
 * for storing configuration options.
 */
trait ValueFunctionTrait
{
    private $valueFunctionTrait = array();

    protected function valueFunction($key, $set = null, $default = null)
    {
        if ($set !== null) {
            $this->valueFunctionTrait[$key] = $set;
        }
        if (isset($this->valueFunctionTrait[$key])) {
            return $this->valueFunctionTrait[$key];
        }
        return $default;
    }
}
