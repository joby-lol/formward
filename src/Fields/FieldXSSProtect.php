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

use \Digraph\Forms\Fields\FieldHidden;

class FieldXSSProtect extends FieldHidden
{
    protected $_checkToken = null;

    protected $_tagAttributes = array(
        'type' => 'hidden'
    );

    public function setName($name)
    {
        parent::setName($name);
        $this->setDefault($this->getToken());
    }

    public function checkToken($oneTime = false)
    {
        if ($this->_checkToken !== null) {
            return $this->_checkToken;
        }
        if ($this->getSubmittedValue() == $this->getToken()) {
            if ($oneTime) {
                $this->getToken(true);
            }
            $this->_checkToken = true;
        } else {
            $this->_checkToken = false;
        }
        return $this->_checkToken;
    }

    public function getToken($regenerate = false)
    {
        $sessPos = '\\Digraph\\Forms\\Fields\\FieldXSSProtect';
        if (!isset($_SESSION[$sessPos])) {
            $_SESSION[$sessPos] = array();
        }
        $myID = $this->getName();
        if ($regenerate || !isset($_SESSION[$sessPos][$myID])) {
            $_SESSION[$sessPos][$myID] = $this->generateToken();
        }
        $this->setValue($_SESSION[$sessPos][$myID]);
        return $_SESSION[$sessPos][$myID];
    }

    private function generateToken()
    {
        return md5(rand());
    }

    public function validate()
    {
        $this->setState(static::STATE_VALID);
        return $this->getState();
    }
}
