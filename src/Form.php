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
namespace Digraph\Forms;

use \Digraph\Forms\AbstractForm;

class Form extends AbstractForm
{
    public function handle($validCallback = null, $invalidCallback = null, $notSubmittedCallback = null)
    {
        if (!$validCallback) {
            $validCallback = Form::STATE_VALID;
        }
        if (!$invalidCallback) {
            $invalidCallback = Form::STATE_INVALID;
        }
        if (!$notSubmittedCallback) {
            $notSubmittedCallback = Form::STATE_NULL;
        }
        if ($this->isSubmitted()) {
            if ($this->validate() === static::STATE_VALID) {
                if (is_callable($validCallback)) {
                    return $validCallback($this);
                } else {
                    return $validCallback;
                }
            } else {
                if (is_callable($invalidCallback)) {
                    return $invalidCallback($this);
                } else {
                    return $invalidCallback;
                }
            }
        }
        if (is_callable($notSubmittedCallback)) {
            return $notSubmittedCallback($this);
        } else {
            return $notSubmittedCallback;
        }
    }
    public function __toString()
    {
        $out = array();
        $out[] = '<div class="'.$this->getClass('Form').'" data-jsdata="'.htmlspecialchars(json_encode($this->_jsdata)).'">';
        $out[] = parent::__toString();
        $out[] = '</div>';
        $out[] = '';
        return implode(PHP_EOL, $out);
    }
}
