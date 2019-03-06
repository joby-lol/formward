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

use \Digraph\Forms\Interfaces\EnumFieldInterface;
use \Digraph\Forms\Fields\AbstractSingleField;

class Dropdown extends AbstractSingleField implements EnumFieldInterface
{
    protected $_options = array();
    protected $_tag = 'select';

    public function buildTag()
    {
        $tag = array();
        $tag[] = parent::buildTag();
        $tag[] = '<option value="">-- select --</option>';
        foreach ($this->_options as $key => $value) {
            if ($this->getValue() == $key) {
                $selected = ' selected';
            } else {
                $selected = '';
            }
            $tag[] = '<option value="'.htmlspecialchars($key).'"'.$selected.'>'.htmlentities(html_entity_decode($value)).'</option>';
        }
        $tag[] = '</'.$this->_tag.'>';
        return implode(PHP_EOL, $tag);
    }

    public function getValue()
    {
        $value = parent::getValue();
        if (isset($this->_options[$value])) {
            return $value;
        }
        return null;
    }

    public function setOptions($options)
    {
        $this->_options = $options;
    }

    public function add($name, $value)
    {
        $this->_options[$name] = $value;
    }

    public function remove($name)
    {
        unset($this->_options[$name]);
    }

    public function clearOptions()
    {
        $this->setOptions(array());
    }
}
