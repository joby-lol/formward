<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;
use \DateTime;

class Time extends AbstractTransformedInput
{
    const FMT = 'H:i';

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->type('time');
        $this->addTip('Time zone: '.date_default_timezone_get());
    }

    protected function transformValue($value)
    {
        if ($value instanceof DateTime) {
            return $value;
        }
        $value = DateTime::createFromFormat(static::FMT, $value);
        if ($value) {
            $value->setDate(0, 0, 0);
        }
        return $value;
    }

    protected function unTransformValue($value)
    {
        return $value->format(static::FMT);
    }
}
