<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;
use \DateTime;

class Date extends AbstractTransformedInput
{
    const FMT = 'Y-m-d';

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->type('date');
        $this->addTip('Time zone: '.date_default_timezone_get());
    }

    protected function transformValue($value)
    {
        $value = DateTime::createFromFormat(static::FMT, $value);
        if ($value) {
            $value->setTime(0, 0);
        }
        return $value;
    }

    protected function unTransformValue($value)
    {
        return $value->format(static::FMT);
    }
}
