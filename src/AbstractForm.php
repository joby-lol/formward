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
namespace Digraph\Forms;

use \Digraph\Forms\Fields\FieldSet;
use \Digraph\Forms\Interfaces\FormInterface;
use \Digraph\Forms\Fields\AbstractFieldSet;

use \Digraph\Forms\Fields\FieldSubmit;
use \Digraph\Forms\Fields\FieldXSSProtect;
use \Digraph\Forms\Fields\FieldHidden;

abstract class AbstractForm extends FieldSet implements FormInterface
{
    protected $_implicitFields = array();

    protected $_submitButton;
    protected $_XSS;
    protected $_submitCheck;

    protected $_oneTimeTokens = true;
    protected $_disableXSS = false;

    protected $_target = '';

    public function disableXSS()
    {
        $this->_disableXSS = true;
        unset($this->_implicitFields['xss']);
    }

    public function getAction()
    {
        return $this->_target;
    }

    public function setAction($target)
    {
        $this->_target = $target;
    }

    public function oneTimeTokens($value = null)
    {
        if ($value === true) {
            $this->_oneTimeTokens = true;
        } elseif ($value === false) {
            $this->_oneTimeTokens = false;
        }
        return $this->_oneTimeTokens;
    }

    public function setMethod($method)
    {
        parent::setMethod($method);
        foreach ($this->_implicitFields as $field) {
            $field->setMethod($method);
        }
    }

    public function __construct($label = null)
    {
        //manage naming
        parent::__construct($label);
        //set up explicit fields
        $this->_submitButton = new FieldSubmit();
        $this->_XSS = new FieldXSSProtect();
        $this->_XSS->setLabel('XSS Protection');
        $this->_submitCheck = new FieldHidden();
        $this->addImplicitField('submit', $this->_submitButton);
        $this->addImplicitField('xss', $this->_XSS);
        $this->addImplicitField('submitted', $this->_submitCheck);
    }

    public function validate()
    {
        $this->setState(static::STATE_VALID);
        foreach ($this->_implicitFields as $id => $field) {
            if ($field->validate() !== static::STATE_VALID) {
                $this->setState(static::STATE_INVALID);
                foreach ($field->validationMessages() as $message) {
                    $this->collectValidationMessage($message);
                }
            }
        }
        if ($this->getState() !== static::STATE_VALID) {
            return $this->getState();
        }
        return parent::validate();
    }

    public function isSubmitted()
    {
        if ($this->_disableXSS) {
            return $this->_submitCheck->getValue() !== null;
        } else {
            return ($this->_submitCheck->getValue() !== null) && $this->_XSS->checkToken($this->oneTimeTokens());
        }
    }

    private function addImplicitField($name, $field)
    {
        $this->_implicitFields[$name] = $field;
        $this->_implicitFields[$name]->setName('_'.$this->getName().'_'.$name);
    }

    public function __toString()
    {
        $out = array();
        if ($this->getLabel()) {
            $out[] = '<h1>'.$this->getLabel().'</h1>';
        }
        $out[] = '<form enctype="multipart/form-data" method="'.$this->getMethod().'" action="'.$this->getAction().'" data-jsdata="'.htmlspecialchars(json_encode($this->_jsdata)).'">';
        foreach ($this->validationMessages() as $message) {
            $out[] = '<div class="validation-error"><a href="#'.$message['field'].'-wrapper" class="validation-error-target">'.$message['fieldLabel'].'</a> '.$message['message'].'</div>';
        }
        $out[] = AbstractFieldSet::__toString();
        foreach ($this->_implicitFields as $id => $field) {
            $out[] = $field->__toString();
        }
        $out[] = '</form>';
        return implode(PHP_EOL, $out);
    }

    public function setName($name)
    {
        parent::setName($name);
        foreach ($this->_implicitFields as $key => $value) {
            $value->setName('_'.$this->getName().'_'.$key);
        }
    }

    public function getXSSToken()
    {
        return $this->_implicitFields['xss']->getDefault();
    }
}
