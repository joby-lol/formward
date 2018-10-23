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
        $this->addTip('Time zone: '.date_default_timezone_get());
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
        if ($set instanceof DateTime) {
            $this['date']->$method($set);
            $this['time']->$method($set);
        }
        $date = $this['date']->$method();
        $time = $this['time']->$method();
        if ($date && $time) {
            $date->setTime($time->format('G'), $time->format('i'));
            return $date;
        }
        return null;
    }
}
