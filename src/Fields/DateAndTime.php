<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;
use \DateTime;

class DateAndTime extends Container
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->wrapContainerItems(false);
        $this['date'] = new Date('Date');
        $this['time'] = new Time('Time');
        $this->addTip('Time zone: '.date_default_timezone_get(), 'timezone');
    }

    public function value($set=null)
    {
        return $this->transformMethod('value', $set);
    }

    public function default($set=null)
    {
        return $this->transformMethod('default', $set);
    }

    protected function transformMethod($method, $set)
    {
        $date = $this['date']->$method($set);
        $time = $this['time']->$method($set);
        return $this->timestamp();
    }

    public function stringValue()
    {
        $date = $this['date']->value();
        $time = $this['time']->value();
        if ($date && $time) {
            return $date.' '.$time;
        }
        return null;
    }

    public function timestamp()
    {
        if ($str = $this->stringValue()) {
            return strtotime($str);
        }
        return null;
    }

    public function value_date($fmt)
    {
        return date($fmt, $this->timestamp());
    }
}
