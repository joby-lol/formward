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
namespace Digraph\Forms\Tests;

use \PHPUnit\Framework\TestCase;

use \Digraph\Forms\Form;

class FormTest extends TestCase
{

    public function testInitiallyNotSubmitted()
    {
        $form = new Form();
        $this->assertFalse($form->isSubmitted());
    }

    public function testInvalidXSS()
    {
        $form = new Form();
        $form->setName('TESTFORM');
        //form should now be called TESTFORM, and its implicit fields should be
        //_TESTFORM_xss and _TESTFORM_submitted
        //with an incorrect xss value, the form should appear not submitted
        $_POST['_TESTFORM_submitted'] = '';
        $_POST['_TESTFORM_xss'] = 'foo';
        $this->assertFalse($form->isSubmitted());
    }

    public function testValidXSS()
    {
        //test with POST
        $form = new Form();
        $form->setName('TESTFORM');
        //now try setting a correct XSS token
        $_POST['_TESTFORM_submitted'] = '';
        $_POST['_TESTFORM_xss'] = $form->getXSSToken();
        $this->assertTrue($form->isSubmitted());
        //test again with GET
        $form = new Form();
        $form->setMethod('get');
        $form->setName('TESTFORM');
        //now try setting a correct XSS token
        $_GET['_TESTFORM_submitted'] = '';
        $_GET['_TESTFORM_xss'] = $form->getXSSToken();
        $this->assertTrue($form->isSubmitted());
    }

    public function testHandleNotSubmitted()
    {
        //test with POST
        $form = new Form();
        $form->setName('TESTFORM');
        $result = $form->handle(
            'valid',
            'invalid',
            'not submitted'
        );
    }
}
