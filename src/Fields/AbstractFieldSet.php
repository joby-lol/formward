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
namespace Digraph\Forms\Fields;

use \Digraph\Forms\Interfaces\AbstractFieldContainerArray;

abstract class AbstractFieldSet extends AbstractFieldContainerArray
{
    protected $_validationMessages = array();
    protected $_name;
    private static $_counter = 0;

    protected $_method = 'POST';

    public function setMethod($method)
    {
        $this->_method = strtoupper($method);
        foreach ($this as $field) {
            $field->setMethod($method);
        }
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function addClass($class, $recursive = null)
    {
        parent::addClass($class, $recursive);
        foreach ($this as $field) {
            $field->addClass($class, $recursive);
        }
    }
    public function removeClass($class, $recursive = null)
    {
        parent::removeClass($class, $recursive);
        foreach ($this as $field) {
            $field->removeClass($class, $recursive);
        }
    }

    public function __construct($label = null)
    {
        parent::__construct($label);
        $name = 'FS-'.self::$_counter++;
        $this->setName($name);
    }

    public function setName($name)
    {
        $this->_name = $name;
        foreach ($this as $key => $value) {
            $value->setName($this->getName().'_'.$key);
        }
    }

    public function getName()
    {
        return $this->_name;
    }

    public function __toString()
    {
        $out = array();
        if ($tips = $this->getTips('FieldSet-tips')) {
            $out[] = $tips;
        }
        foreach ($this as $id => $field) {
            $out[] = $field->__toString();
        }
        return implode(PHP_EOL, $out);
    }

    public function collectValidationMessage($message)
    {
        $this->_validationMessages[] = $message;
    }

    public function validate()
    {
        $this->setState(static::STATE_VALID);
        foreach ($this as $id => $field) {
            if ($field->validate() !== static::STATE_VALID) {
                $this->setState(static::STATE_INVALID);
                foreach ($field->validationMessages() as $message) {
                    $this->collectValidationMessage($message);
                }
            }
        }
        return $this->getState();
    }

    public function validationMessages()
    {
        return $this->_validationMessages;
    }
}
