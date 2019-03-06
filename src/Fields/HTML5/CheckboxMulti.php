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

use \Digraph\Forms\Fields\HTML5\Checkbox;
use \Digraph\Forms\Fields\AbstractMultiSelectComboField;

class CheckboxMulti extends AbstractMultiSelectComboField
{
    public function construct($label = null)
    {
    }

    public function clearOptions()
    {
        foreach ($this as $key => $field) {
            unset($this[$key]);
        }
    }

    public function setOptions($options)
    {
        $this->clearOptions();
        foreach ($options as $key => $value) {
            $this->add($key, $value);
        }
    }

    public function add($key, $value)
    {
        $this[$key] = new Checkbox($value);
    }

    public function remove($key)
    {
        unset($this[$key]);
    }

    public function getValue()
    {
        $out = array();
        foreach ($this as $key => $field) {
            if ($field->getValue()) {
                $out[$key] = $field->getLabel('true');
            }
        }
        return $out;
    }
}
