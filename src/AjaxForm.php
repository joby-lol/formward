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
namespace Digraph\Forms;

use \Digraph\Forms\Form;
use \Digraph\Forms\Interfaces\AjaxFormInterface;
use \Digraph\Forms\Interfaces\AbstractFieldContainerArray;

class AjaxForm extends Form implements AjaxFormInterface
{
    protected $_ajaxFields = array();
    protected static $_resources = array();

    protected $_handle = null;
    public function handle($validCallback = null, $invalidCallback = null, $notSubmittedCallback = null)
    {
        if ($this->_handle === null) {
            static::constructResources($this);
            $this->handleResources();
            $this->handleAjax();
            $this->_handle = parent::handle($validCallback, $invalidCallback, $notSubmittedCallback);
        }
        return $this->_handle;
    }

    public static function constructResources($obj)
    {
        foreach ($obj as $field) {
            if ($field instanceof AbstractFieldContainerArray) {
                static::constructResources($field);
            }
            if (method_exists($field, 'constructResources')) {
                $field->constructResources();
            }
            if (method_exists($field, 'getAjaxToken')) {
                $field->getAjaxToken();
            }
        }
    }

    public function __construct($label = null)
    {
        parent::__construct($label);
        AjaxForm::registerExternalCSS('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', 'font-awesome');
        AjaxForm::registerExternalInitScript('https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js', 'modernizr');
        AjaxForm::registerExternalInitScript('https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js', 'jquery');
        AjaxForm::registerInternalInitScript(
            file_get_contents(__DIR__.'/_resources/core-js.js'),
            'core-js'
        );
        AjaxForm::registerInternalCSS(
            file_get_contents(__DIR__.'/_resources/core.css'),
            'core-css'
        );
    }

    public static function registerInternalLoadScript($content, $index = null)
    {
        return static::registerResource('script', 'load', 'internal', $content, $index);
    }

    public static function registerInternalInitScript($content, $index = null)
    {
        return static::registerResource('script', 'init', 'internal', $content, $index);
    }

    public static function registerExternalLoadScript($content, $index = null)
    {
        return static::registerResource('script', 'load', 'external', $content, $index);
    }

    public static function registerExternalInitScript($content, $index = null)
    {
        return static::registerResource('script', 'init', 'external', $content, $index);
    }

    public static function registerInternalCSS($content, $index = null)
    {
        return static::registerResource('css', 'init', 'internal', $content, $index);
    }

    public static function registerExternalCSS($content, $index = null)
    {
        return static::registerResource('css', 'init', 'external', $content, $index);
    }

    public static function loadResourcesHTML($bundle = false)
    {
        $out = array();
        //external css
        foreach (static::listResources('css', 'load', 'external') as $key => $resource) {
            $out[] = '<link rel="stylesheet" href="'.$resource['content'].'"><!-- '.$key.' -->';
        }
        //external scripts
        foreach (static::listResources('script', 'load', 'external') as $key => $resource) {
            $out[] = '<script src="'.$resource['content'].'"></script><!-- '.$key.' -->';
        }
        //internal scripts
        if ($bundle) {
            $out[] = '<script src="'.static::resourcesUrlPrefix().'bundleLoadScriptsInternal"></script>';
        } else {
            foreach (static::listResources('script', 'load', 'internal') as $key => $resource) {
                $out[] = '<script src="'.static::resourcesUrlPrefix().$key.'"></script>';
            }
        }
        //output
        return implode(PHP_EOL, $out).PHP_EOL;
    }

    public static function initResourcesHTML($bundle = false)
    {
        $out = array();
        //internal css
        if ($bundle) {
            $out[] = '<link rel="stylesheet" href="'.static::resourcesUrlPrefix().'bundleCSSInternal">';
        } else {
            foreach (static::listResources('css', 'init', 'internal') as $key => $resource) {
                $out[] = '<link rel="stylesheet" href="'.static::resourcesUrlPrefix().$key.'">';
            }
        }
        //external scripts
        foreach (static::listResources('script', 'init', 'external') as $key => $resource) {
            $out[] = '<script src="'.$resource['content'].'"></script><!-- '.$key.' -->';
        }
        //internal scripts
        if ($bundle) {
            $out[] = '<script src="'.static::resourcesUrlPrefix().'bundleInitScriptsInternal"></script>';
        } else {
            foreach (static::listResources('script', 'init', 'internal') as $key => $resource) {
                $out[] = '<script src="'.static::resourcesUrlPrefix().$key.'"></script>';
            }
        }
        //output
        return implode(PHP_EOL, $out).PHP_EOL;
    }

    public static function resourcesHTML($bundle = false)
    {
        $out = array();
        if ($bundle) {
            //external css
            foreach (static::listResources('css', 'init', 'external') as $key => $resource) {
                $out[] = '<link rel="stylesheet" href="'.$resource['content'].'"><!-- Digraph resource: '.$key.' -->';
            }
            //external scripts
            foreach (static::listResources('script', 'load', 'external') as $key => $resource) {
                $out[] = '<script src="'.$resource['content'].'"></script><!-- Digraph resource: '.$key.' -->';
            }
            foreach (static::listResources('script', 'init', 'external') as $key => $resource) {
                $out[] = '<script src="'.$resource['content'].'"></script><!-- Digraph resource: '.$key.' -->';
            }
            //internal bundles
            $out[] = '<link rel="stylesheet" href="'.static::resourcesUrlPrefix().'bundleCSSInternal">';
            $out[] = '<script src="'.static::resourcesUrlPrefix().'bundleAllScriptsInternal"></script>';
        } else {
            $out[] = static::initResourcesHTML();
            $out[] = static::loadResourcesHTML();
        }
        return implode(PHP_EOL, $out).PHP_EOL;
    }

    protected static function resourcesUrlPrefix()
    {
        if (!$_GET) {
            $return = '?';
        } else {
            $out = array();
            foreach ($_GET as $key => $value) {
                $out[] = urlencode($key).'='.urlencode($value);
            }
            $return = '?'.implode('&', $out).'&';
        }
        return $return.'ResourceToken=';
    }

    private static function listResources($type, $loadat, $location)
    {
        $out = array();
        foreach (static::$_resources as $index => $resource) {
            if ($resource['type'] == $type && $resource['loadat'] == $loadat && $resource['location'] == $location) {
                $out[$index] = $resource;
            }
        }
        return $out;
    }

    private static function registerResource($type, $loadat, $location, $content, $index = null)
    {
        $resource = array(
            'type' => $type,
            'loadat' => $loadat,
            'location' => $location,
            'content' => $content
        );
        if ($index === null) {
            $index = md5(implode('|', $resource));
        }
        static::$_resources[$index] = $resource;
        return $index;
    }

    private static function outputInternalResource($token)
    {
        $resource = static::$_resources[$token];
        switch ($resource['type']) {
            case 'script':
                $mime = 'application/javascript';
                break;
            case 'css':
                $mime = 'text/css';
                break;
            default:
                $mime = 'text/plain';
                break;
        }
        header("Content-type: $mime");
        if ($resource['type'] == 'script' && $resource['loadat'] == 'init') {
            echo '$(function() {'.$resource['content'].'});';
        } else {
            echo $resource['content'];
        }
        return $mime;
    }

    private static function bundleLoadScriptsInternal()
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-type: text/javascript');
        $out = array();
        foreach (static::listResources('script', 'load', 'internal') as $key => $resource) {
            $out[] = '/* '.$key.' */';
            $out[] = '(function(){';
            $out[] = $resource['content'];
            $out[] = '}());'.PHP_EOL;
        }
        echo implode(PHP_EOL, $out);
    }

    private static function bundleInitScriptsInternal()
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-type: text/javascript');
        $out = array();
        foreach (static::listResources('script', 'init', 'internal') as $key => $resource) {
            $out[] = '/* '.$key.' */';
            $out[] = '$(function() {';
            $out[] = $resource['content'];
            $out[] = '});'.PHP_EOL;
        }
        echo implode(PHP_EOL, $out);
    }

    private static function bundleAllScriptsInternal()
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-type: text/javascript');
        $out = array();
        //load
        foreach (static::listResources('script', 'load', 'internal') as $key => $resource) {
            $out[] = '/* '.$key.' */';
            $out[] = '(function(){';
            $out[] = $resource['content'];
            $out[] = '}());'.PHP_EOL;
        }
        //init
        foreach (static::listResources('script', 'init', 'internal') as $key => $resource) {
            $out[] = '/* '.$key.' */';
            $out[] = '$(function() {';
            $out[] = $resource['content'];
            $out[] = '});'.PHP_EOL;
        }
        echo implode(PHP_EOL, $out);
    }

    private static function bundleCSSInternal()
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-type: text/css');
        $out = array();
        foreach (static::listResources('css', 'init', 'internal') as $key => $resource) {
            $out[] = '/* '.$key.' */';
            $out[] = $resource['content'].PHP_EOL;
        }
        echo implode(PHP_EOL, $out);
    }

    public static function handleResources($noExit = false, $token=null)
    {
        if ($token === null) {
            if (!isset($_REQUEST['ResourceToken'])) {
                return false;
            }
            $token = $_REQUEST['ResourceToken'];
        }
        if ($token) {
            //bundling load scripts
            if ($token == 'bundleLoadScriptsInternal') {
                static::bundleLoadScriptsInternal($token);
                if (!$noExit) {
                    exit();
                }
                return 'application/javascript';
            }
            //bundling init scripts
            if ($token == 'bundleInitScriptsInternal') {
                static::bundleInitScriptsInternal($token);
                if (!$noExit) {
                    exit();
                }
                return 'application/javascript';
            }
            //bundling all scripts
            if ($token == 'bundleAllScriptsInternal') {
                static::bundleAllScriptsInternal($token);
                if (!$noExit) {
                    exit();
                }
                return 'application/javascript';
            }
            //bundling CSS
            if ($token == 'bundleCSSInternal') {
                static::bundleCSSInternal($token);
                if (!$noExit) {
                    exit();
                }
                return 'text/css';
            }
            //handle requests
            if (isset(static::$_resources[$token]) && static::$_resources[$token]['location'] == 'internal') {
                $mime = static::outputInternalResource($token);
                if (!$noExit) {
                    exit();
                }
                return $mime;
            }
        }
        return false;
    }

    public function handleAjax($noExit = false)
    {
        //must be a valid AjaxToken in the request
        if ($this->checkAjaxToken()) {
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Content-type: application/json');
            $field = $this->_ajaxFields[$_REQUEST['AjaxToken']];
            $query = $_REQUEST['query'];
            $output = $field->handleAjax(json_decode($query, true));
            echo json_encode($output, JSON_FORCE_OBJECT);
            if (!$noExit) {
                exit();
            }
            return true;
        }
        return false;
    }

    public function getAjaxToken(&$field, $regenerate = false)
    {
        $fieldName = $field->getName();
        if ($regenerate || !isset($_SESSION['\\Digraph\\Forms']['AjaxToken'][$fieldName])) {
            $_SESSION['\\Digraph\\Forms']['AjaxToken'][$fieldName] = md5(rand());
        }
        $this->_ajaxFields[$_SESSION['\\Digraph\\Forms']['AjaxToken'][$fieldName]] = $field;
        return $_SESSION['\\Digraph\\Forms']['AjaxToken'][$fieldName];
    }

    public function checkAjaxToken()
    {
        if (isset($_REQUEST['AjaxToken'])) {
            return isset($this->_ajaxFields[$_REQUEST['AjaxToken']]);
        }
        return false;
    }
}
