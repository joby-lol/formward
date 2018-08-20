<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\SystemFields;

use Formward\FieldInterface;

class Submit extends SystemButton
{
    protected function fieldAttributes()
    {
        $out = parent::fieldAttributes();
        $out['type'] = 'submit';
        return $out;
    }
}
