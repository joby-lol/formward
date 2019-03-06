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

abstract class AbstractAutocomplete extends AbstractAjaxField implements AutocompleteInterface
{
    public function __construct($label = null)
    {
        parent::__construct($label);
        $this->addClass('Autocomplete');
        $this->_tagAttributes['autocomplete'] = 'off';
        $this->placeholder('Autocomplete field');
    }

    public function validateValue($value)
    {
        $result = $this->autocomplete($value);
        if (isset($result[$value])) {
            return static::STATE_VALID;
        }
        return static::STATE_INVALID;
    }

    protected function _constraint_limitToCompletions(&$field, $opt)
    {
        if ($valid = $field->validateValue($field->getSubmittedValue())) {
            return $valid;
        }
        return "Only autocompleted values are accepted. You can't enter any other text.";
    }

    public function limitToCompletions()
    {
        $this->addConstraint('limitToCompletions');
    }

    public function handleAjax($query = false)
    {
        $r = $this->autocomplete($query['q']);
        if (is_array($r)) {
            $r2 = array();
            foreach ($r as $key => $value) {
                $key = trim($key);
                if (!is_array($value)) {
                    $r2[$key] = array(
                        'label' => trim($value),
                        'json' => (json_encode(array(
                            'label' => $value
                        )))
                    );
                } else {
                    $r2[$key] = $value;
                    $r2[$key]['json'] = (json_encode($r2[$key]));
                }
            }
            return array('r'=>$r2);
        } else {
            return array('e'=>$r);
        }
    }

    public function constructResources()
    {
        parent::constructResources();
        $this->registerInternalInitScript(file_get_contents(__DIR__.'/_resources/Autocomplete.js'), 'core-autocomplete-js');
        $this->registerInternalCSS(file_get_contents(__DIR__.'/_resources/Autocomplete.css'), 'core-autocomplete-css');
    }
}
