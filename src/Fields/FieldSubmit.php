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

use \Digraph\Forms\Fields\Field;

class FieldSubmit extends AbstractSingleField
{
    protected $_tagAttributes = array(
        'type' => 'submit',
        'value' => 'Submit Form'
    );

    public function validate()
    {
        $this->setState(static::STATE_VALID);
        return $this->getState();
    }

    public function __toString()
    {
        $out = array();
        $out[] = '<div class="'.$this->getClass('Submit').'">';
        $out[] = '  '.$this->buildTag();
        $out[] = '</div>';
        return implode(PHP_EOL, $out);
    }

    public function buildTag()
    {
        $values = $this->_tagAttributes;
        $values['class'] = $this->getClass('fieldTag');
        //build attributes into markup
        $attributes = array();
        foreach ($values as $key => $value) {
            $attributes[] = htmlspecialchars($key).'="'.htmlspecialchars($value).'"';
        }
        $tag = array(
        $this->_tag,
        implode(' ', $attributes)
        );
        return "<".implode(' ', $tag).">";
    }
}
