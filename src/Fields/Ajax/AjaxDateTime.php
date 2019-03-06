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

//I don't know if this is actually that useful in a mobile world, but
class AjaxDateTime extends AbstractAutocomplete
{
    public $_displayFormat = 'F j, Y, g:i a';

    public function autocomplete($q)
    {
        $q = preg_replace('/ +/', ' ', $q);
        $q = preg_replace('/,? at /i', ', ', $q);
        $q = preg_replace('/^(monday|tuesday|wednesday|thursday|friday|saturday|sunday),? /i', '', $q);
        if ($parsed = strtotime($q)) {
            return array(
                date($this->_displayFormat, $parsed) => date($this->_displayFormat, $parsed)
            );
        }
        return 'Input could not be parsed';
    }

    public function construct($label = null)
    {
        $this->_tagAttributes['type'] = 'datetime';
        $this->_tagAttributes['placeholder'] = '';
    }
}
