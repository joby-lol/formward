<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class DateAndTimeRange extends Container
{
    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->wrapContainerItems(false);
        $this->addTip('Time zone: '.date_default_timezone_get());
        $this->addClass('inline-children');
        $this['start'] = new DateAndTime('Start');
        $this['start']->removeTip('timezone');
        $this['end'] = new DateAndTime('End');
        $this['end']->removeTip('timezone');
        $this->addValidatorFunction(
            'bothornone',
            function (&$field) {
                if (($field['start']->value() && !$field['end']->value()) || (!$field['start']->value() && $field['end']->value())) {
                    return 'Range must include a start and end';
                }
                return true;
            }
        );
        $this->addValidatorFunction(
            'ordered',
            function (&$field) {
                if ($field['start']->value() && $field['end']->value()) {
                    if ($field['start']->value() >= $field['end']->value()) {
                        return 'Start must be before end';
                    }
                }
                return true;
            }
        );
    }
}
