<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;
use \DateTime;

class Date extends Input
{
    const FMT = 'Y-m-d';

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->type('date');
        $this->addTip('Time zone: '.date_default_timezone_get());
    }

    public function timestamp()
    {
        return strtotime($this->value());
    }

    protected function normalizeSet($set = null)
    {
        if (is_string($set)) {
            $set = strtotime($set);
        }
        if (is_int($set)) {
            return date(static::FMT, $set);
        }
        return null;
    }

    public function default($set=null)
    {
        $set = $this->normalizeSet($set);
        //pass off to parent
        return parent::default($set);
    }
}
