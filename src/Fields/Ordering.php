<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class Ordering extends TextArea
{
    protected $allowDelete = false;
    protected $allowAddition = false;
    protected $opts = [];

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->addClass('formward-ordering-field');
        /*
        Validator checking that allowAddition is true or nothing is added
         */
        $this->addValidatorFunction(
             'addition',
             function (&$field) use ($s) {
                 $value = $field->value();
                 if (!$value || $field->allowAddition()) {
                     return true;
                 }
                 if ($field->added()) {
                     return 'Adding items is not allowed. Please remove: '.implode(', ', $field->added());
                 }
                 return true;
             }
         );
        /*
        Validator for checking that either allowDelete is true or nothing is missing
         */
        $this->addValidatorFunction(
            'deletion',
            function (&$field) use ($s) {
                $value = $field->value();
                if (!$value || $field->allowDelete()) {
                    return true;
                }
                if ($field->deleted()) {
                    return 'Deleting items is not allowed. Please include: '.implode(', ', $field->deleted());
                }
                return true;
            }
        );
    }

    public function allowDelete(bool $set=null) : bool
    {
        if ($set !== null) {
            if ($set) {
                $this->addClass('deletion-allowed');
            } else {
                $this->removeClass('deletion-allowed');
            }
            $this->allowDelete = $set;
        }
        return $this->allowDelete;
    }

    public function deleted()
    {
        $out = [];
        $value = $this->value();
        foreach ($this->opts as $k => $v) {
            if (!in_array($k, $value)) {
                $out[] = $k;
            }
        }
        return $out;
    }

    public function added()
    {
        $out = [];
        $value = $this->value();
        foreach ($value as $k => $v) {
            if (!isset($this->opts[$v])) {
                $out[] = $v;
            }
        }
        return $out;
    }

    public function allowAddition(bool $set=null) : bool
    {
        if ($set !== null) {
            if ($set) {
                $this->addClass('addition-allowed');
            } else {
                $this->removeClass('addition-allowed');
            }
            $this->allowAddition = $set;
        }
        return $this->allowAddition;
    }

    public function opts(array $opts = null) : array
    {
        if ($opts !== null) {
            $opts = array_unique($opts);
            $this->opts = $opts;
            $this->default(implode(PHP_EOL, array_keys($opts)));
        }
        return $this->opts;
    }

    public function htmlValue()
    {
        $value = $this->value();
        $value = $value + array_map(
            function ($e) {
                return 'DELETE:'.$e;
            },
            $this->deleted()
        );
        return implode(PHP_EOL, $value);
    }

    public function transformValue($value)
    {
        if (is_array($value)) {
            return $value;
        }
        $value = preg_split('/[\r\n]+/', trim($value));
        $value = array_unique($value);
        $value = array_map("trim", $value);
        $value = array_filter($value, function ($e) {
            return $e && strpos($e, 'DELETE:') !== 0;
        });
        return $value;
    }

    protected function htmlAttributes()
    {
        $attr = parent::htmlAttributes();
        $attr['data-opts'] = json_encode($this->opts);
        return $attr;
    }

    public function unTransformValue($value)
    {
        if ($value === null) {
            return null;
        }
        if (is_string($value)) {
            return $value;
        }
        return implode(PHP_EOL, $value);
    }
}
