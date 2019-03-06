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

interface AjaxFieldInterface
{
    public function handleAjax($query = false);
    public function getAjaxToken($regenerate = false);
    public function &getForm();

    public function registerInternalLoadScript($content, $index = null);
    public function registerInternalInitScript($content, $index = null);
    public function registerExternalLoadScript($content, $index = null);
    public function registerExternalInitScript($content, $index = null);
    public function registerExternalCSS($content, $index = null);
}
