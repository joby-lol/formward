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

class Time extends Field
{
    public function __construct($label = null)
    {
        parent::__construct($label);
        $this->_tagAttributes['type'] = 'time';
        $this->_tagAttributes['step'] = '60';
        $this->addTip('hh:mm:ss (24 hour time)');
        $this->addTip(date_default_timezone_get());
        $this->addConstraint('regex', array(
            'regex' => '/^([0-1][0-9]|2[0-3]):([0-5][0-9])(:([0-5][0-9]))?$/',
            'message' => 'Enter a 24-hour time in the form hh:mm:ss'
        ));
        $defaultStep = 60*30;
        $time = round(time()/$defaultStep)*$defaultStep;
        $this->setDefault(date('H:i:s', $time));
    }
}
