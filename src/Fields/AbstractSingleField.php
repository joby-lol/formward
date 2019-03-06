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

use \Digraph\Forms\Interfaces\AbstractField;
use \Digraph\Forms\Interfaces\SingleFieldInterface;

abstract class AbstractSingleField extends AbstractField implements SingleFieldInterface
{
    protected $_name;
    protected $_tag = 'input';
    protected $_tagAttributes = array();
    protected $_default = null;
    protected $_value = null;
    protected $_constraints = array();
    protected $_method = 'POST';

    public function __toString()
    {
        $out = array();
        $out[] = '<div id="'.$this->getName().'-wrapper" class="'.$this->getClass('Field').'" data-jsdata="'.htmlspecialchars(json_encode($this->_jsdata)).'">';
        if ($this->getLabel() !== null) {
            $out[] = '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
        }
        $out[] = $this->buildTag();
        if ($tips = $this->getTips('Field-tips')) {
            $out[] = $tips;
        }
        $out[] = '</div>';
        return implode(PHP_EOL, $out);
    }

    public function setMethod($method)
    {
        $this->_method = strtoupper($method);
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function required()
    {
        $this->addConstraint('required', true);
        $this->addClass('required');
        $this->_tagAttributes['required'] = 'true';
    }

    public function disabled()
    {
        $this->addClass('disabled');
        $this->_tagAttributes['disabled'] = true;
    }

    public function readonly()
    {
        $this->addClass('readonly');
        $this->_tagAttributes['readonly'] = true;
    }

    public function maxlength($maxlength)
    {
        $this->addConstraint('maxlength', $maxlength);
        $this->_tagAttributes['maxLength'] = $maxlength;
    }

    public function placeholder($placeholder)
    {
        $this->_tagAttributes['placeholder'] = $placeholder;
    }

    public function exactLength($length)
    {
        $this->addConstraint('regex', array(
            'regex' => '/^.{'.$length.'}$/',
            'message' => 'Must be exactly '.$length.' characters'
        ));
        $this->addTip('Must be exactly '.$length.' characters');
    }

    public function setDefault($value)
    {
        $this->_default = $value;
    }

    public function getDefault()
    {
        return $this->_default;
    }

    public function getValue()
    {
        if ($this->_value === null) {
            $this->_value = $this->getSubmittedValue();
        }
        if ($this->_value === null) {
            return $this->_default;
        }
        return $this->_value;
    }

    public function setValue($value)
    {
        $this->_value = $value;
    }

    public function getSubmittedValue()
    {
        if ($this->getMethod() == 'GET' && isset($_GET[$this->getName()])) {
            return trim($_GET[$this->getName()]);
        }
        if ($this->getMethod() == 'POST' && isset($_POST[$this->getName()])) {
            return trim($_POST[$this->getName()]);
        }
        return null;
    }

    public function buildTag()
    {
        $values = $this->_tagAttributes;
        if (!isset($values['name'])) {
            $values['name'] = $this->getName();
        }
        if (!isset($values['id'])) {
            $values['id'] = $this->getName();
        }
        $values['class'] = $this->getClass('FieldTag');
        if (!isset($values['value'])) {
            $values['value'] = $this->getValue();
        }
        //build attributes into markup
        $attributes = array();
        foreach ($values as $key => $value) {
            if ($value === null) {
                $attributes[] = htmlspecialchars($key);
            } else {
                $attributes[] = htmlspecialchars($key).'="'.htmlspecialchars($value).'"';
            }
        }
        $tag = array(
        $this->_tag,
        implode(' ', $attributes)
        );
        return "<".implode(' ', $tag).">";
    }

    public function addConstraint($constraint, $options = true)
    {
        $this->_constraints[] = array(
            'constraint' => $constraint,
            'options' => $options
        );
    }

    public function _constraint_required(&$field, $options)
    {
        if ($field->getValue() !== null && $field->getValue() !== '') {
            return static::STATE_VALID;
        }
        return 'Field is required';
    }

    public function _constraint_regex(&$field, $options)
    {
        if (!preg_match($options['regex'], $field->getValue())) {
            return $options['message'];
        }
        return static::STATE_VALID;
    }

    public function validate()
    {
        $this->setState(static::STATE_VALID);
        foreach ($this->_constraints as $constraint) {
            $options = $constraint['options'];
            $constraint = $constraint['constraint'];
            if (!$this->getValue() && $constraint != 'required') {
                continue;
            }
            if (is_callable($constraint)) {
                $result = $constraint($this, $options);
            } elseif (method_exists($this, $constraint = '_constraint_'.$constraint)) {
                $result = $this->$constraint($this, $options);
            } else {
                $result = 'Constraint "'.$constraint.'" could not be processed';
            }
            if ($result !== static::STATE_VALID) {
                $this->addValidationMessage($result);
                $this->setState(static::STATE_INVALID);
                break;
            }
        }
        return $this->getState();
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }
}
