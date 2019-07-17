<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields\PDF;

use Formward\FieldInterface;
use Formward\Fields\File;

class FilePDF extends File
{
    use PDFParsingTrait;

    protected $maxPages = null;

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
                return $field->pagesValidatorFunction();
            }
        );
    }

    public function pagesValidatorFunction()
    {
        if ($this->pages()) {
            if ($this->pages() > $this->maxPages()) {
                return 'PDF must be no more than '.$this->maxPages.' page'.(($this->maxPages==1)?'':'s').' (file has '.$this->pages().'). If your PDF page count is being read incorrectly, please re-save your PDF using different settings.';
            }
        }
        return true;
    }

    public function pages()
    {
        if (!$this->value()) {
            return null;
        }
        return $this->pdfPageCount($this->value()['file']);
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
