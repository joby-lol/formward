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

class Number extends Field
{
    public function __construct($label = null)
    {
        parent::__construct($label);
        $this->_tagAttributes['type'] = 'number';
        $this->addConstraint('regex', array(
            'regex' => "/^\-?[0-9]+$/",
            'message' => 'Enter a whole number'
        ));
    }

    public function min($min)
    {
        $this->_tagAttributes['min'] = $min;
        $this->addTip('Must be greater than or equal to '.$min, 'min');
        $this->addConstraint(
            function ($field, $message) use ($min) {
                if ($field->getValue() < $min) {
                    return $message;
                }
                return true;
            },
            'Enter a number greater than or equal to '.$min
        );
    }

    public function max($max)
    {
        $this->_tagAttributes['max'] = $max;
        $this->addTip('Must be less than or equal to '.$max, 'max');
        $this->addConstraint(
            function ($field, $message) use ($max) {
                if ($field->getValue() > $max) {
                    return $message;
                }
                return true;
            },
            'Enter a number less than or equal to '.$max
        );
    }
}
