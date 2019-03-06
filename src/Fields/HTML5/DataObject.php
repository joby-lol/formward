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

class DataObject extends Field
{
    protected $_class = null;

    public function _constraint_dataobjectexists($args)
    {
        $class = $this->_class;
        if (!$class::idExists($this->getValue())) {
            return 'Enter a valid ID';
        }
        return static::STATE_VALID;
    }

    public function setClass($class)
    {
        $this->_class = $class;
        $this->addTip("Must be a valid object ID of type $class", 'class-constraint');
        $this->addConstraint('dataobjectexists', true);
    }
}
