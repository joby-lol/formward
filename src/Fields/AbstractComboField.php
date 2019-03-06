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

use \Digraph\Forms\Fields\AbstractFieldSet;
use \Digraph\Forms\Interfaces\SingleFieldInterface;

abstract class AbstractComboField extends AbstractFieldSet implements SingleFieldInterface
{
    protected $_constraints = array();

    public function addConstraint($constraint, $options = true)
    {
        $this->_constraints[] = array(
            'constraint' => $constraint,
            'options' => $options
        );
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

    public function __toString()
    {
        $count = $this->fieldCount();
        $countingClasses = ' ComboField-count-'.$count;
        for ($i=2; $i <= $count; $i++) {
            if ($count%$i == 0) {
                $countingClasses .= ' ComboField-mod-'.$i;
            }
        }
        $out[] = '<div class="'.$this->getClass('ComboField').$countingClasses.'" id="'.$this->getName().'-wrapper" data-jsdata="'.htmlspecialchars(json_encode($this->_jsdata)).'">';
        if ($this->getLabel() !== null) {
            $out[] = '  <label>'.$this->getLabel().'</label>';
        }
        $out[] = parent::__toString();
        $out[] = '</div>';
        return implode(PHP_EOL, $out);
    }

    public function getValue()
    {
        $out = array();
        foreach ($this as $name => $field) {
            $out[$name] = $field->getValue();
        }
        return $out;
    }

    public function getSubmittedValue()
    {
        $out = array();
        foreach ($this as $name => $field) {
            $out[$name] = $field->getSubmittedValue();
        }
        return $out;
    }

    public function setValue($values)
    {
        foreach ($values as $name => $value) {
            $this[$name]->setValue($value);
        }
    }

    public function getDefault()
    {
        $out = array();
        foreach ($this as $name => $field) {
            $out[$name] = $field->getDefault();
        }
        return $out;
    }

    public function setDefault($values)
    {
        if (!$values) {
            return;
        }
        foreach ($values as $name => $value) {
            if (isset($this[$name])) {
                $this[$name]->setDefault($value);
            }
        }
    }

    public function required()
    {
        foreach ($this as $field) {
            $field->required();
        }
    }

    public function disabled()
    {
        foreach ($this as $field) {
            $field->disabled();
        }
    }

    public function readonly()
    {
        foreach ($this as $field) {
            $field->readonly();
        }
    }

    public function maxlength($values)
    {
        foreach ($values as $name => $value) {
            $this[$name]->maxlength($value);
        }
    }

    public function placeholder($values)
    {
        foreach ($values as $name => $value) {
            $this[$name]->placeholder($value);
        }
    }
}
