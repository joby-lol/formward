<?php
/* Formward | https://github.com/jobyone/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;
use Symfony\Component\Yaml\Yaml as YamlParser;

class YAML extends Textarea
{
    const FMT_NAME = 'YAML';
    public $tag = 'textarea';
    public $selfClosing = false;

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->addTip('Content must be valid formatted '.static::FMT_NAME.' data');
        $this->addValidatorFunction(
            'parseable',
            function (&$field) {
                return $field->validate_parseable();
            }
        );
    }

    public function validate_parseable()
    {
        try {
            $this->transformValue(AbstractField::value());
            return true;
        } catch (\Exception $e) {
            return 'Error parsing '.static::FMT_NAME.': '.$e->getMessage();
        }

        return true;
    }

    public function htmlValue()
    {
        try {
            return parent::htmlValue();
        } catch (\Exception $e) {
            return AbstractField::value();
        }
    }

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
        if (is_null($value)) {
            return [];
        }
        return YamlParser::parse($value);
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
        return YamlParser::dump($value);
    }
}
