<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

abstract class AbstractTransformedInput extends Input
{
    abstract protected function transformValue($value);
    abstract protected function unTransformValue($value);

    public function htmlValue()
    {
        if ($value = $this->value()) {
            return $this->unTransformValue($value);
        }
        return null;
    }

    public function value($set = null)
    {
        return $this->transformMethod('value', $set);
    }

    public function default($set = null)
    {
        return $this->transformMethod('default', $set);
    }

    protected function transformMethod($method, $set)
    {
        if ($set !== null) {
            $set = $this->transformValue($set);
        }
        if ($set) {
            parent::$method($this->unTransformValue($set));
        }
        try {
            return $this->transformValue(parent::$method());
        } catch (\Exception $e) {
            return parent::$method();
        }
    }
}
