<?php
/**
 * Digraph CMS: Forms
 * https://github.com/digraphcms/digraph-forms

 * Copyright (c) 2017 Joby Elliott <joby@byjoby.com>

 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 */
namespace Digraph\Forms\Fields\HTML5;

use \Digraph\Forms\Fields\AbstractComboField;

class DateTime extends AbstractComboField
{
    public function construct($label=null)
    {
        $this['date'] = new Date('Date');
        $this['time'] = new Time('Time');
    }

    public function setDefault($timestamp)
    {
        parent::setDefault($this->timestamp2array($timestamp));
    }

    public function setValue($timestamp)
    {
        parent::setValue($this->timestamp2array($timestamp));
    }

    public function getDefault()
    {
        return $this->array2timestamp(parent::getDefault());
    }

    public function getValue()
    {
        return $this->array2timestamp(parent::getValue());
    }

    protected function timestamp2array($timestamp)
    {
        return array(
            'date' => date('Y-m-d', $timestamp),
            'time' => date('H:i:s', $timestamp)
        );
    }

    protected function array2timestamp($array)
    {
        return strtotime(implode(' ', $array));
    }
}
