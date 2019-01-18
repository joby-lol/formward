<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;
use \DateTime;

class Time extends Date
{
    const FMT = 'H:i';

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->type('time');
        $this->addTip('Time zone: '.date_default_timezone_get());
    }
}
