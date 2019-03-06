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

use \Digraph\Forms\Fields\AbstractSingleField;
use \Digraph\Forms\Interfaces\HTML5FieldInterface;

class Field extends AbstractSingleField implements HTML5FieldInterface
{
    public function min($min)
    {
        $this->_tagAttributes['min'] = $min;
    }

    public function _constraint_maxlength(&$field, $max)
    {
        if (strlen($field->getValue()) <= $max) {
            return static::STATE_VALID;
        }
        return 'Maximum length is '.$max.' characters';
    }

    public function max($max)
    {
        $this->_tagAttributes['max'] = $max;
        $this->addTip('Maximum length: '.$max.' characters', 'maxlength');
        $this->addConstraint('maxlength', $max);
    }

    public function pattern($pattern)
    {
        $this->_tagAttributes['pattern'] = $pattern;
    }

    public function step($step)
    {
        $this->_tagAttributes['step'] = $step;
    }
}
