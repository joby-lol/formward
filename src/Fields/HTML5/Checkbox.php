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

class Checkbox extends Field
{
    public function __construct($label = null)
    {
        parent::__construct($label);
        $this->_tagAttributes['type'] = 'checkbox';
        $this->_tagAttributes['value'] = '1';
    }

    public function setTagValue($value)
    {
        $this->_tagAttributes['value'] = $value;
    }

    public function getTagValue()
    {
        return $this->_tagAttributes['value'];
    }

    public function getSubmittedValue()
    {
        if ($this->getParentTop()->isSubmitted()) {
            if ($this->getMethod() == 'GET' && isset($_GET[$this->getName()])) {
                return $_GET[$this->getName()] == $this->getTagValue();
            }
            if ($this->getMethod() == 'POST' && isset($_POST[$this->getName()])) {
                return $_POST[$this->getName()] == $this->getTagValue();
            }
            return false;
        }
        return null;
    }

    public function buildTag()
    {
        if ($this->getValue()) {
            $this->_tagAttributes['checked'] = 'true';
        } else {
            unset($this->_tagAttributes['checked']);
        }
        return parent::buildTag();
    }

    public function _constraint_required(&$field, $options)
    {
        if ($field->getValue()) {
            return static::STATE_VALID;
        }
        return 'Field is required';
    }

    public function __toString()
    {
        $out = array();
        $out[] = '<div id="'.$this->getName().'-wrapper" class="'.$this->getClass('Field').'" data-jsdata="'.htmlspecialchars(json_encode($this->_jsdata)).'">';
        $this->_tagAttributes['id'] = $this->getName();
        $out[] = $this->buildTag();
        if ($this->getLabel() !== null) {
            $out[] = '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
        }
        if ($tips = $this->getTips('Field-tips')) {
            $out[] = $tips;
        }
        $out[] = '</div>';
        return implode(PHP_EOL, $out);
    }
}
