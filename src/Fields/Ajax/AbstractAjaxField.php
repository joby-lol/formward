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
namespace Digraph\Forms\Fields\Ajax;

use \Digraph\Forms\Fields\HTML5\Field;
use \Digraph\Forms\Interfaces\AjaxFieldInterface;

abstract class AbstractAjaxField extends Field implements AjaxFieldInterface
{
    public function constructResources()
    {
    }

    public function __toString()
    {
        $out = array();
        $out[] = '<div id="'.$this->getName().'-wrapper" class="'.$this->getClass('Field').' AjaxField-wrapper" data-token="'.$this->getAjaxToken().'" data-jsdata="'.htmlspecialchars(json_encode($this->_jsdata)).'">';
        if ($this->getLabel() !== null) {
            $out[] = '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
        }
        $out[] = $this->buildTag();
        if ($tips = $this->getTips('Field-tips')) {
            $out[] = $tips;
        }
        $out[] = '</div>';
        return implode(PHP_EOL, $out);
    }

    public function getAjaxToken($regenerate = false)
    {
        return $this->getForm()->getAjaxToken($this, $regenerate);
    }

    public function &getForm()
    {
        return $this->getParentTop();
    }

    public function setParent(&$parent)
    {
        parent::setParent($parent);
    }

    public function getFormClass()
    {
        return '\\Digraph\\Forms\\AjaxForm';
    }

    public function registerInternalLoadScript($content, $index = null)
    {
        $class = $this->getFormClass();
        $class::registerInternalLoadScript($content, $index);
    }
    public function registerInternalInitScript($content, $index = null)
    {
        $class = $this->getFormClass();
        $class::registerInternalInitScript($content, $index);
    }
    public function registerExternalLoadScript($content, $index = null)
    {
        $class = $this->getFormClass();
        $class::registerExternalLoadScript($content, $index);
    }
    public function registerExternalInitScript($content, $index = null)
    {
        $class = $this->getFormClass();
        $class::registerExternalInitScript($content, $index);
    }
    public function registerInternalCSS($content, $index = null)
    {
        $class = $this->getFormClass();
        $class::registerInternalCSS($content, $index);
    }
    public function registerExternalCSS($content, $index = null)
    {
        $class = $this->getFormClass();
        $class::registerExternalCSS($content, $index);
    }
}
