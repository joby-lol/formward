<?php
/**
 * Digraph CMS: Forms
 * https://github.com/digraphcms/digraph-forms

 * Copyright (c) 2020 Joby Elliott <joby@byjoby.com>

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

use Digraph\Forms\Fields\HTML5\Dropdown;

class FieldSetSelectable extends FieldSet
{
    /**
     * @var Dropdown
     */
    protected $selector;

    protected $_required = false;

    public function __construct($label = null)
    {
        $this['__selector'] = $this->selector = new Dropdown('');
        parent::__construct($label);
    }

    public function __toString()
    {
        $wrapperIDs = array();
        foreach ($this as $k => $v) {
            if ($k !== '__selector') {
                if ($v instanceof FieldSet) {
                    $wrapperIDs[$k] = $v->getName();
                }else {
                    $wrapperIDs[$k] = $v->getName() . '-wrapper';
                }
            }
        }
        $script = file_get_contents(__DIR__ . '/_resources/FieldSetSelectable.js');
        $script = str_replace('{{selectorID}}', $this->selector->getName(), $script);
        $script = str_replace('/**allWrappers**/', '= ' . json_encode($wrapperIDs), $script);
        $script = "<script>$script</script>";
        return parent::__toString() . $script;
    }

    public function validate()
    {
        if ($this->_required) {
            $this[$this->selector->getValue()]->required();
        }
        return parent::validate();
    }

    public function required()
    {
        $this->selector->required();
        $this->_required = true;
    }

    public function offsetSet($offset, $value)
    {
        if ($offset != '__selector') {
            $this->selector->add($offset, $value->getLabel());
        }
        return parent::offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        if ($offset != '__selector') {
            $this->selector->remove($offset);
        }
        return parent::offsetUnset($offset);
    }

    public function setDefault($value)
    {
        $this->selector->setDefault(@$value['type']);
        if ($this->selector->getValue()) {
            $this[$this->selector->getValue()]->setDefault(@$value['value']);
        }
    }

    public function getValue()
    {
        if ($this->selector->getValue()) {
            return array(
                'type' => $this->selector->getValue(),
                'value' => $this[$this->selector->getValue()]->getValue(),
            );
        } else {
            return null;
        }
    }
}
