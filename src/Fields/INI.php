<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;

class INI extends YAML
{
    const FMT_NAME = 'INI';
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
        $parsed = parse_ini_string($value, true);
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
        return $this->ini_string($value);
    }

    protected function ini_string($arr)
    {
        $outLayer1 = '';
        $outSections = '';
        foreach ($arr as $section => $values) {
            $section = $this->ini_escape($section);
            if (is_array($values)) {
                $outSections .= '['.$section.']'.PHP_EOL;
                foreach ($values as $key => $value) {
                    $key = $this->ini_escape($key);
                    $value = $this->ini_escape($value);
                    $outSections .= "$key=$value".PHP_EOL;
                }
                $outSections .= PHP_EOL;
            } else {
                $values = $this->ini_escape($values);
                $outLayer1 .= "$section=$values".PHP_EOL;
            }
        }
        $out = $outLayer1?$outLayer1.PHP_EOL:'';
        $out .= $outSections;
        return $out;
    }

    protected function ini_escape($value)
    {
        $quoted = (preg_match("/[\\\\\"';#=:\a\b\t\r\n]/", $value));
        if ($quoted) {
            $chars = ['"'];
        } else {
            $chars = ['\\','"',"\0","\a","\b","\t","\r","\n",';','#','=',':'];
        }
        foreach ($chars as $char) {
            $value = str_replace($char, "\\$char", $value);
        }
        if ($quoted) {
            $value = "\"$value\"";
        }
        return $value;
    }
}
