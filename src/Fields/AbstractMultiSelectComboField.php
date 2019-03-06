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
use \Digraph\Forms\Interfaces\MultiSelectFieldInterface;

abstract class AbstractMultiSelectComboField extends AbstractComboField implements MultiSelectFieldInterface
{

    protected $_constraints = array();

    private function s($n)
    {
        if ($n == 1) {
            return '';
        }
        return 's';
    }

    public function _constraint_minCount($field, $min)
    {
        if (count($this->getValue()) < $min) {
            return 'You must select at least '.$min.' option'.$this->s($min);
        }
        return static::STATE_VALID;
    }

    public function _constraint_maxCount($field, $max)
    {
        if (count($this->getValue()) > $max) {
            return 'You must not select more than '.$max.' option'.$this->s($max);
        }
        return static::STATE_VALID;
    }

    public function min($min)
    {
        $this->addClass('required');
        if ($min != 1) {
            $this->addTip('Select at least '.$min.' items');
        }
        $this->addConstraint('minCount', $min);
    }

    public function max($max)
    {
        $this->addTip('Select at most '.$max.' item'.$this->s($max));
        $this->addConstraint('maxCount', $max);
    }

    public function required()
    {
        $this->min(1);
    }

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
        foreach ($this->_constraints as $constraint) {
            $options = $constraint['options'];
            $constraint = $constraint['constraint'];
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
}
