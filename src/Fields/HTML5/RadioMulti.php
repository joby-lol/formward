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

use \Digraph\Forms\Fields\HTML5\Radio;
use \Digraph\Forms\Fields\HTML5\CheckboxMulti;

class RadioMulti extends CheckboxMulti
{
    public function setName($name)
    {
        $this->_name = $name;
        foreach ($this as $key => $value) {
            $value->setName($this->_name.'_opt');
        }
    }

    public function add($key, $value)
    {
        $this[$key] = new Radio($value);
        $this[$key]->setName($this->getName().'-val');
        $this[$key]->setTagValue($key);
    }

    public function getValue()
    {
        foreach ($this as $key => $field) {
            if ($field->getValue()) {
                return $key;
            }
        }
        return null;
    }

    public function setValue($value)
    {
        parent::setValue(array($value => true));
    }

    public function setDefault($value)
    {
        if (isset($this[$value])) {
            parent::setDefault(array($value => true));
        }
    }

    public function _constraint_minVal($field, $min)
    {
        if ($this->getValue() === null) {
            return 'You must select an option';
        }
        return static::STATE_VALID;
    }

    public function _constraint_maxVal($field, $max)
    {
        //max number of responses is meaningless -- it can only be 1
        return static::STATE_VALID;
    }

    public function required()
    {
        $this->min(1);
    }

    public function min($min)
    {
        if ($min > 0) {
            $this->addClass('required');
            $this->addConstraint('minVal', $min);
        }
    }

    public function max($max)
    {
        //max number of responses is meaningless -- it can only be 1
    }
}
