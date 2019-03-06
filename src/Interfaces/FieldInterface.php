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

interface FieldInterface
{
    const STATE_NULL = null;
    const STATE_VALID = true;
    const STATE_INVALID = false;

    public function __construct($label = null);
    public function __toString();

    public function setState($state);
    public function getState();
    public function validate();

    public function clearState();
    public function clearValidationMessages();
    public function validationMessages();
    public function addValidationMessage($message);

    public function setName($name);
    public function getName();
    public function getMyName();

    public function getClass($base);

    public function setLabel($label);
    public function getLabel($force = null);

    public function setMethod($method);
    public function getMethod();

    public function &getParent();
    public function &getParentTop();
    public function setParent(&$parent);

    public function addClass($class, $recursive = null);
    public function removeClass($class, $recursive = null);
}
