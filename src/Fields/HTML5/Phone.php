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

class Phone extends Field
{
    public function getValue()
    {
        $value = parent::getValue();
        $value = preg_replace('/[^0-9]/', '', $value);
        if (strlen($value) == 7) {
            $value = preg_replace('/^([0-9]{3})([0-9]{4})$/', '$1-$2', $value);
        } elseif (strlen($value) == 10) {
            $value = preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{4})$/', '($1) $2-$3', $value);
        } else {
            $value = preg_replace('/^([0-9]{1,3})([0-9]{3})([0-9]{3})([0-9]{4})$/', '+$1 ($2) $3-$4', $value);
        }
        return $value;
    }

    public function __construct($label = null)
    {
        parent::__construct($label);
        $this->_tagAttributes['type'] = 'phone';
        $this->addTip('');
        $this->addConstraint('regex', array(
            'regex' => "/^((\+[0-9]{1,3} )?\([0-9]{3}\) )?[0-9]{3}\-[0-9]{4}$/i",
            'message' => 'Valid phone numbers must be: 7 digits, 10 digits, or 10 digits plus a country code.'
        ));
    }
}
