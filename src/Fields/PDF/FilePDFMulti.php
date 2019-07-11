<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields\PDF;

use Formward\FieldInterface;
use Formward\Fields\FileMulti;

class FilePDFMulti extends FileMulti
{
    use PDFParsingTrait;

    protected $maxPages = null;
    protected $maxPagesPer = null;

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->attr('accept', '.pdf');
        /* add validation of PDF MIME type */
        $this->addValidatorFunction(
            'pdftype',
            function (&$field) {
                foreach ($field->value() as $value) {
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
                return $field->pagesValidatorFunction();
            }
        );
    }

    public function pagesValidatorFunction()
    {
        //validate individual page counts
        if ($this->maxPagesPer) {
            foreach($this->value() as $f) {
                if ($this->pdfPageCount($f['file']) > $this->maxPagesPer) {
                    return $f['name'].' must be no more than '.$this->maxPagesPer.' page'.($this->maxPagesPer==1?'':'s. If your PDF page count is being read incorrectly, please re-save your PDF using different settings.');
                }
            }
        }
        //validate total page count
        if ($this->maxPages && $this->totalPages()) {
            if ($this->totalPages() > $this->maxPages) {
                return 'All files must be no more than '.$this->maxPages.' total page'.($this->maxPages==1?'':'s. If your PDF page count is being read incorrectly, please re-save your PDF using different settings.');
            }
        }
        //true if no errors were found
        return true;
    }

    public function totalPages()
    {
        if (!$this->value()) {
            return null;
        }
        $count = 0;
        foreach($this->value() as $f) {
            $count += $this->pdfPageCount($f['file']);
        }
        return $count;
    }

    public function maxPages($set = null)
    {
        if ($set !== null) {
            $this->maxPages = $set;
        }
        if ($this->maxPages) {
            $this->addTip('Maximum total page count: '.$this->maxPages, 'maxpages');
        } else {
            $this->removeTip('maxpages');
        }
        return $this->maxPages;
    }

    public function maxPagesPer($set = null)
    {
        if ($set !== null) {
            $this->maxPagesPer = $set;
        }
        if ($this->maxPagesPer) {
            $this->addTip('Maximum page count per file: '.$this->maxPagesPer, 'maxpagesper');
        } else {
            $this->removeTip('maxpagesper');
        }
        return $this->maxPagesPer;
    }
}
