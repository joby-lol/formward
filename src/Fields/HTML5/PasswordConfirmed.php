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

class PasswordConfirmed extends AbstractComboField
{
    public function construct($label=null)
    {
        $this['a'] = new Password('Enter password');
        $this['b'] = new Password('Confirm password');
    }

    public function validate()
    {
        parent::validate();
        if ($this['a']->getValue() != $this['b']->getValue()) {
            $this->setState(static::STATE_INVALID);
            $this->addValidationMessage('The passwords entered must match');
        }
        return $this->getState();
    }

    public function setDefault($value)
    {
        $this['a']->setDefault($value);
        $this['b']->setDefault($value);
    }

    public function setValue($value)
    {
        $this['a']->setValue($value);
        $this['b']->setValue($value);
    }

    public function getDefault()
    {
        return $this['a']->getDefault();
    }

    public function getValue()
    {
        return $this['a']->getValue();
    }
}
