<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;

class JSON extends YAML
{
    const FMT_NAME = 'JSON';
    public $tag = 'textarea';
    public $selfClosing = false;

    /**
     * Convert actual value into user-side format
     * for this field an exception needs to be thrown if this fails, so that it
     * can be captured during validation and shown to the user.
     */
    protected function transformValue($value)
    {
        if (is_array($value)) {
            return $value;
        }
        $parsed = json_decode($value, true);
        if ($parsed === null) {
            throw new \Exception("Unspecified parse error");
        }
        return $parsed;
    }

    /**
     * Convert user-side format into actual value
     */
    protected function unTransformValue($value)
    {
        if ($value === null) {
            return null;
        }
        if (is_string($value)) {
            return $value;
        }
        return json_encode($value, JSON_PRETTY_PRINT);
    }
}
