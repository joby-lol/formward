<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class FilePDF extends File
{
    protected $maxPages = null;
    public $pages = null;

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->attr('accept', '.pdf');
        /* add validation of PDF MIME type */
        $this->addValidatorFunction(
            'pdftype',
            function (&$field) {
                if ($value = $field->value()) {
                    if ($value['type'] != 'application/pdf') {
                        return 'Only PDF files are allowed';
                    }
                }
                return true;
            }
        );
        /* add validation of page count */
        $this->addValidatorFunction(
            'pdfpages',
            function (&$field) {
                if ($field->maxPages() && $field->pages()) {
                    if ($field->pages() > $field->maxPages()) {
                        return 'Please upload a PDF with no more than '.$field->maxPages().' page'.($field->maxPages==1?'':'s. If your PDF page count is being read incorrectly, please re-save your PDF using different settings.');
                    }
                }
                return true;
            }
        );
    }

    public function pages()
    {
        if (!$this->value()) {
            return null;
        }
        if ($this->pages === null) {
            $this->pages = 0;
            if (!$this->scanFileForPageCount(0, 1024)) {
                $this->scanFileForPageCount(512, 1024);
            }
        }
        return $this->pages;
    }

    protected function scanFileForPageCount($offset, $size)
    {
        //return false if no file is uploaded
        if (!$this->value()) {
            return false;
        }
        //open file
        $handle = fopen($this->value()['file'], "rb");
        if (false === $handle) {
            exit("Failed to open stream to URL");
        }
        //offset scan starting point
        if ($offset) {
            fread($handle, $offset);
        }
        //scan file
        $found = false;
        while (!feof($handle)) {
            $chunk = fread($handle, $size);
            if ($this->parseStringForPageCount($chunk)) {
                $found = true;
                break;
            }
        }
        fclose($handle);
        return $found;
    }

    /**
     * Parse a chunk of a PDF as a string to see if a page count can be found.
     * Based on http://de77.com/php/extract-title-author-and-number-of-pages-from-pdf-with-php
     * Returns true if it finds a page count so that the function calling it
     * can know to stop reading the file.
     *
     * Original copyright notice:
     * Author: de77.com
     * Licence: MIT
     * Homepage: http://de77.com/php/extract-title-author-and-number-of-pages-from-pdf-with-php
     * Version: 21.07.2010
     *
     * @param string $string
     * @return bool
     */
    protected function parseStringForPageCount($string)
    {
        if (preg_match("/\/N\s+([0-9]+)/", $string, $found)) {
            $this->pages = $found[1];
            return true;
        } else {
            $pos = strpos($string, '/Type /Pages ');
            if ($pos !== false) {
                $pos2 = strpos($string, '>>', $pos);
                $string = substr($string, $pos, $pos2 - $pos);
                $pos = strpos($string, '/Count ');
                $this->pages = (int) substr($string, $pos+7);
                return true;
            }
        }
        return false;
    }


    public function maxPages($set = null)
    {
        if ($set !== null) {
            $this->maxPages = $set;
        }
        if ($this->maxPages) {
            $this->addTip('Maximum page count: '.$this->maxPages, 'maxpages');
        } else {
            $this->removeTip('maxpages');
        }
        return $this->maxPages;
    }
}
