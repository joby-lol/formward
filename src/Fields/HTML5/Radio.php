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

class Radio extends Checkbox
{
    public function __construct($label = null)
    {
        parent::__construct($label);
        $this->_tagAttributes['type'] = 'radio';
        $this->_tagAttributes['value'] = '1';
    }

    public function __toString()
    {
        $out = array();
        $out[] = '<div id="'.$this->getName().'-'.$this->getTagValue().'-wrapper" class="'.$this->getClass('Field').'" data-jsdata="'.htmlspecialchars(json_encode($this->_jsdata)).'">';
        $this->_tagAttributes['id'] = $this->getName().'-'.$this->getTagValue();
        $out[] = $this->buildTag();
        if ($this->getLabel() !== null) {
            $out[] = '<label for="'.$this->getName().'-'.$this->getTagValue().'">'.$this->getLabel().'</label>';
        }
        if ($tips = $this->getTips('Field-tips')) {
            $out[] = $tips;
        }
        $out[] = '</div>';
        return implode(PHP_EOL, $out);
    }
}
