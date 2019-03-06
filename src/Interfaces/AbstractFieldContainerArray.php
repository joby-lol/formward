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

use \Digraph\Forms\Interfaces\FieldContainerInterface;
use \Digraph\Forms\Interfaces\AbstractField;

abstract class AbstractFieldContainerArray extends AbstractField implements FieldContainerInterface, \ArrayAccess, \Iterator
{
    protected $fields = array();
    protected $iterMap = array();
    protected $iterPos = 0;

    public function fieldCount()
    {
        return count($this->fields);
    }

    public function insertAfter($pos, $offset, $value)
    {
        //set up field
        $value->setName($this->getName() . '_' .$offset);
        $value->setMethod($this->getMethod());
        $value->setParent($this);
        //insert
        $keys = array_keys($this->fields);
        $pos = array_search($pos, $keys);
        $left = array_slice($this->fields, 0, $pos+1, true);
        $left[$offset] = $value;
        $right = array_slice($this->fields, $pos+1, null, true);
        $this->fields = $left + $right;
        $this->buildIterMap();
    }

    public function unshift($offset, $value)
    {
        $this->fields = array_merge(array($offset=>$value), $this->fields);
        $value->setName($this->getName() . '_' .$offset);
        $value->setMethod($this->getMethod());
        $value->setParent($this);
        $this->buildIterMap();
    }

    protected function buildIterMap()
    {
        $this->iterMap = array();
        foreach ($this->fields as $key => $value) {
            $this->iterMap[] = $key;
        }
    }
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->fields[] = $value;
            $offset = count($this->fields);
        } else {
            $this->fields[$offset] = $value;
        }
        $value->setName($this->getName() . '_' .$offset);
        $value->setMethod($this->getMethod());
        $value->setParent($this);
        $this->buildIterMap();
    }
    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }
    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
        $this->buildIterMap();
    }

    protected function &getRef($offset)
    {
        return $this->fields[$offset];
    }

    public function offsetGet($offset)
    {
        if (isset($this->fields[$offset])) {
            return $this->getRef($offset);
        }
        return null;
    }

    public function rewind()
    {
        $this->iterPos = 0;
    }
    public function &current()
    {
        if (isset($this->fields[$this->key()])) {
            return $this->fields[$this->key()];
        }
        return null;
    }
    public function key()
    {
        return isset($this->iterMap[$this->iterPos]) ? $this->iterMap[$this->iterPos] : null;
    }
    public function next()
    {
        $this->iterPos++;
    }
    public function valid()
    {
        return isset($this->fields[$this->key()]);
    }
}
