<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields\PDF;

use Formward\FieldInterface;
use Formward\Fields\FileMulti;

trait PDFParsingTrait
{
    protected $pdfPageCount = [];

    public function pdfPageCount($file)
    {
        if (!$this->pdfPageCount[$file]) {
            $this->pdfPageCount[$file] =  $this->scanFileForPageCount($file, 2048);
        }
        return $this->pdfPageCount[$file];
    }

    protected function scanFileForPageCount($file, $size, $offset = 0)
    {
        //open file
        $handle = fopen($file, "rb");
        if (false === $handle) {
            exit("Failed to open stream to URL");
        }
        //offset scan starting point
        if ($offset) {
            fread($handle, $offset);
        }
        //scan file
        $found = null;
        while (!feof($handle)) {
            $chunk = fread($handle, $size);
            if ($found = $this->parseStringForPageCount($chunk)) {
                break;
            }
        }
        fclose($handle);
        //set offset if it is currently zero
        if (!$offset && !$found) {
            $found = $this->scanFileForPageCount($file,$size,$size/2);
        }
        //return value
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
        $pos = strpos($string, '/Type /Pages ');
        if ($pos !== false) {
            $pos2 = strpos($string, '>>', $pos);
            $string = substr($string, $pos, $pos2 - $pos);
            $pos = strpos($string, '/Count ');
            return (int) substr($string, $pos+7);
        } elseif (preg_match("/\/N\s+([0-9]+)/", $string, $found)) {
            return $found[1];
        }
        return false;
    }
}
