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
namespace Digraph\Forms\Fields\Files;

class MultiFile extends SimpleFile
{
    public function __construct($label = null)
    {
        parent::__construct($label);
        $this->_tagAttributes['multiple'] = null;
    }

    public function __toString()
    {
        $this->_tagAttributes['value'] = '';
        $this->_tagAttributes['name'] = $this->getName().'[]';
        return parent::__toString();
    }

    public function size($bytes)
    {
        $this->addTip("Maximum total size: ".static::sizeHR($bytes), 'size');
        $this->addConstraint('fsize', $bytes);
    }

    public function getValue()
    {
        $return = array();
        if (isset($_FILES[$this->getName()])) {
            foreach ($_FILES[$this->getName()]['error'] as $i => $error) {
                if ($error !== 0) {
                    continue;
                }
                $return[] = array(
                    'name' => $_FILES[$this->getName()]['name'][$i],
                    'type' => $_FILES[$this->getName()]['type'][$i],
                    'tmp_name' => $_FILES[$this->getName()]['tmp_name'][$i],
                    'size' => $_FILES[$this->getName()]['size'][$i]
                );
            }
        }
        return $return;
    }

    protected function _constraint_fsize(&$field, $bytes)
    {
        $size = 0;
        foreach ($this->getValue() as $file) {
            $size += $file['size'];
        }
        if ($size > $bytes) {
            return "Total upload size can't be more than ".static::sizeHR($bytes);
        }
        return static::STATE_VALID;
    }
}
