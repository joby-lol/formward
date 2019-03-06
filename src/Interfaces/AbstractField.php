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
namespace Digraph\Forms\Interfaces;

use \Digraph\Forms\Interfaces\FieldInterface;

abstract class AbstractField implements FieldInterface
{
    protected $_state = null;
    protected $_validationMessages = array();
    protected $_additionalClasses = array();
    protected $_label = null;
    protected $_parent = null;
    protected $_tips = array();
    protected $_jsdata = array();

    public function addTip($tip, $index = null)
    {
        if ($index) {
            $this->_tips[$index] = $tip;
        } else {
            $this->_tips[] = $tip;
        }
    }

    public function getTips($class)
    {
        $out = array();
        foreach ($this->_tips as $index => $tip) {
            $classes = array(
                $class,
                $class.'-'.$index
            );
            $out[] = '<div class="'.implode(' ', $classes).'">'.$tip.'</div>';
        }
        return implode(PHP_EOL, $out);
    }

    public function setParent(&$parent)
    {
        $this->_parent = $parent;
    }

    public function &getParent()
    {
        return $this->_parent;
    }

    public function &getParentTop()
    {
        if ($this->getParent() === null) {
            return $this;
        }
        return $this->getParent()->getParentTop();
    }

    public function __construct($label = null)
    {
        if ($label !== null) {
            $this->setLabel($label);
        }
        $this->construct($label);
    }

    public function construct($label = null)
    {
    }

    public function setLabel($label)
    {
        $this->_label = $label;
    }

    public function getLabel($force = null)
    {
        $label = $this->_label;
        if (!$label && $force) {
            $label = $this->getName();
        }
        return $label;
    }

    public function addClass($class, $recursive = null)
    {
        $this->_additionalClasses[] = $class;
        $this->_additionalClasses = array_unique($this->_additionalClasses);
    }

    public function removeClass($class, $recursive = null)
    {
        $this->_additionalClasses = array_diff(
            $this->_additionalClasses,
            array($class)
        );
    }
    public function getMyName()
    {
        $name = $this->getName();
        $name = explode('_', $name);
        return array_pop($name);
    }

    public function getClass($base)
    {
        $class = get_called_class();
        $class = explode('\\', $class);
        $class = array_pop($class);
        $classes = array(
            $base,
            $base.'-name-'.$this->getName(),
            $base.'-myname-'.$this->getMyName(),
            $base.'-class-'.$class
        );
        if ($this->getState() === static::STATE_VALID) {
                $classes[] = $base.'-state-valid';
        } elseif ($this->getState() === static::STATE_INVALID) {
            $classes[] = $base.'-state-invalid';
        }
        if ($this->_additionalClasses) {
            $classes[] = implode(' ', $this->_additionalClasses);
        }
        return implode(' ', $classes);
    }

    public function setState($state)
    {
        if ($state != static::STATE_NULL && $state != static::STATE_VALID && $state != static::STATE_VALID) {
            return false;
        }
        $this->_state = $state;
        return true;
    }

    public function getState()
    {
        return $this->_state;
    }

    public function clearState()
    {
        $this->setState(static::STATE_NULL);
        $this->clearValidationMessages();
    }

    public function clearValidationMessages()
    {
        $this->_validationMessages = array();
    }

    public function validationMessages()
    {
        return $this->_validationMessages;
    }

    public function addValidationMessage($message)
    {
        $this->_validationMessages[] = array(
            'field' => $this->getName(),
            'fieldLabel' => $this->getLabel(true),
            'message' => $message
        );
    }
}
