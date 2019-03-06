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

use \Digraph\DataObject\DataObjectInterface;
use \Digraph\Forms\Fields\FieldSet;

use \Digraph\Forms\Fields\HTML5\Field;
use \Digraph\Forms\Fields\Ajax\AjaxDateTime;
use \Digraph\Forms\Fields\HTML5\Number;
use \Digraph\Forms\Fields\HTML5\RadioMulti;
use \Digraph\Forms\Fields\HTML5\Dropdown;
use \Digraph\Forms\Fields\HTML5\TextArea;
use \Digraph\Forms\Fields\HTML5\DataObject;
use \Digraph\Forms\Fields\HTML5\Checkbox;
use \Digraph\Forms\Fields\HTML5\Phone;

use \Digraph\Forms\Fields\FieldHidden;

class DataObjectForm extends AjaxForm implements \Digraph\Forms\Interfaces\DataObjectFormInterface
{
    protected $_obj = null;
    protected $_map = null;
    protected $_class = null;
    protected $_className = null;
    protected $_fieldSets = array();
    protected $_hideFields = array();
    protected $_includeSystem = false;
    protected $_boundFields = array();
    protected $_oneTimeTokens = true;

    public function &getObject()
    {
        return $this->_obj;
    }

    public function handle($validCallback = null, $invalidCallback = null, $notSubmittedCallback = null)
    {
        $result = parent::handle($validCallback, $invalidCallback, $notSubmittedCallback);
        if ($this->getState() == static::STATE_VALID) {
            $this->saveBound();
        }
        return $result;
    }

    protected function getBoundValue($name)
    {
        $map = $this->_map[$name];
        $typeTrans = '_getBoundValue_type_'.$map['form']['type'];
        $value = $this->_obj->$name;
        if (method_exists($this, $typeTrans)) {
            $value = $this->$typeTrans($name, $value);
        }
        return $value;
    }

    protected function saveBoundValue_transform($name, $value)
    {
        $map = $this->_map[$name];
        $typeTrans = '_saveBoundValue_type_'.$map['form']['type'];
        $value = $this->_boundFields[$name]->getValue();
        if (method_exists($this, $typeTrans)) {
            $value = $this->$typeTrans($name, $value);
        }
        return $value;
    }

    public function saveBound()
    {
        foreach ($this->_boundFields as $name => $field) {
            if ($name == 'do_id') {
                continue;
            }
            $this->saveBoundValue($name, $field->getValue());
        }
        if ($this['do_id']->getSubmittedValue()) {
            $this->_obj->forceID($this['do_id']->getSubmittedValue());
        }
        if ($this->_obj->idExists($this['do_id']->getSubmittedValue())) {
            //updating an existing object
            return $this->_obj->update();
        } else {
            //creating a new object
            return $this->_obj->create();
        }
    }

    protected function saveBoundValue($name, $value)
    {
        $field = $this->_boundFields[$name];
        $map = $this->_map[$name];
        $value = $this->saveBoundValue_transform($name, $value);
        $this->_obj->$name = $value;
    }

    public function bindObject(&$obj, $fieldSets = array(), $hideFields = array(), $includeSystem = false)
    {
        if (!($obj instanceof DataObjectInterface)) {
            throw new \Exception("DataObjectForm can only bind to objects that implement DataObjectInterface", 1);
        }
        $this->_class = get_class($obj);
        $this->_obj = $obj;
        $this->_map = $obj->getMap();
        $this->_className = preg_replace('/.+\\\/', '', $this->_class);
        $this->_fieldSets = $fieldSets;
        $this->_hideFields = $hideFields;
        $this->_includeSystem = $includeSystem;
        $this->bindFields();
    }

    public function bindClass($class, $fieldSets = array(), $hideFields = array(), $includeSystem = false)
    {
        $obj = new $class;
        $this->bindObject($obj, $fieldSets, $hideFields, $includeSystem);
    }

    protected function &buildField($name)
    {
        $mapEntry = $this->_map[$name];
        //handle do_id on its own
        if ($name == 'do_id') {
            $this->_boundFields['do_id'] = new FieldHidden('DataObject ID');
            $this->_boundFields['do_id']->setDefault($this->_obj->do_id);
            return $this->_boundFields['do_id'];
        }
        //determine whether this should be included
        $include = true;
        if (!isset($mapEntry['form'])) {
            $return = null;
            return $return;//without form information in the map, we can't do anything
        }
        if (isset($mapEntry['system']) && $mapEntry['system'] && !$this->_includeSystem) {
            $include = false;
        }
        //return if included
        if ($include) {
            $builder = '_buildField_'.$mapEntry['form']['type'];
            if (!method_exists($this, $builder)) {
                $builder = '_buildField_default';
            }
            $field = $this->$builder($name, $mapEntry);
            if ($value = $this->getBoundValue($name)) {
                $field->setDefault($value);
            }
            if (isset($mapEntry['form']['required']) && $mapEntry['form']['required']) {
                $field->required();
            }
            if (isset($mapEntry['form']['hide']) && $mapEntry['form']['hide']) {
                $field->addClass('hidden');
            }
            if (isset($mapEntry['form']['max']) && $mapEntry['form']['max']) {
                $field->max($mapEntry['form']['max']);
            }
            if (isset($mapEntry['form']['exactLength'])) {
                $field->exactLength($mapEntry['form']['exactLength']);
            }
            if (isset($mapEntry['form']['tips'])) {
                foreach ($mapEntry['form']['tips'] as $key => $value) {
                    $key = 'do-'.$key;
                    $field->addTip($value, $key);
                }
            }
            if (isset($mapEntry['form']['constraints'])) {
                foreach ($mapEntry['form']['constraints'] as $constraint => $arg) {
                    $field->addConstraint($constraint, $arg);
                }
            }
            $this->_boundFields[$name] = $field;
            return $this->_boundFields[$name];
        }
        $return = null;
        return $return;
    }

    protected function &_buildField_default($name, $mapEntry)
    {
        $field = new Field($mapEntry['form']['label']);
        return $field;
    }

    protected function &_buildField_checkbox($name, $mapEntry)
    {
        $field = new Checkbox($mapEntry['form']['label']);
        return $field;
    }

    protected function &_buildField_phone($name, $mapEntry)
    {
        $field = new Phone($mapEntry['form']['label']);
        return $field;
    }

    protected function _saveBoundValue_type_datetime($name, $value)
    {
        return strtotime($value);
    }

    protected function _getBoundValue_type_datetime($name, $value)
    {
        if ($value == '') {
            return '';
        }
        return date('F j, Y, g:i a', $value);
    }

    protected function &_buildField_datetime($name, $mapEntry)
    {
        $field = new AjaxDateTime($mapEntry['form']['label']);
        return $field;
    }

    protected function &_buildField_number($name, $mapEntry)
    {
        $field = new Number($mapEntry['form']['label']);
        return $field;
    }

    protected function &_buildField_dataobject($name, $mapEntry)
    {
        $field = new DataObject($mapEntry['form']['label']);
        $field->setClass($mapEntry['form']['objectclass']);
        return $field;
    }

    protected function &_buildField_textarea($name, $mapEntry)
    {
        $field = new TextArea($mapEntry['form']['label']);
        return $field;
    }

    private function buildField_getOptions($mapEntry)
    {
        $options = array();
        if (isset($mapEntry['form']['enum'])) {
            $options = $mapEntry['form']['enum'];
        } else {
            foreach ($mapEntry['enum'] as $opt) {
                $options[$opt] = $opt;
            }
        }
        return $options;
    }

    protected function &_buildField_radio($name, $mapEntry)
    {
        $field = new RadioMulti($mapEntry['form']['label']);
        $field->setOptions($this->buildField_getOptions($mapEntry));
        return $field;
    }

    protected function &_buildField_pickone($name, $mapEntry)
    {
        $options = $this->buildField_getOptions($mapEntry);
        if (count($options) <= 6) {
            $field = new RadioMulti($mapEntry['form']['label']);
        } else {
            $field = new Dropdown($mapEntry['form']['label']);
        }
        $field->setOptions($options);
        return $field;
    }

    protected function bindFields()
    {
        $fields = array();
        $fieldSets = $this->_fieldSets;
        if (!isset($fieldSets['default'])) {
            $fieldSets['default'] = array();
        }
        //build a list of fields
        foreach ($this->_map as $name => $mapEntry) {
            $fields[] = $name;
        }
        //sort non-specified fields into default fieldset
        $specifiedFields = array();
        foreach ($fieldSets as $fs) {
            foreach ($fs as $field) {
                $specifiedFields[] = $field;
            }
        }
        $fields = array_diff($fields, $specifiedFields);
        $fieldSets['default'] = array_merge($fieldSets['default'], $fields);
        //force do_id
        $fieldSets['default'][] = 'do_id';
        //add actual fields to this object
        $i = 0;
        foreach ($fieldSets as $name => $fields) {
            if ($name != 'default') {
                $i++;
                $this['DOFS-'.$i] = new FieldSet($name);
            }
            foreach ($fields as $fieldName) {
                if (!($field = $this->buildField($fieldName))) {
                    continue;
                }
                if ($name != 'default') {
                    $this['DOFS-'.$i][$fieldName] = $field;
                } else {
                    $this[$fieldName] = $field;
                }
            }
        }
    }
}
