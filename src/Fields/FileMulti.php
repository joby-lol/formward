<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class FileMulti extends Input
{
    protected $fileArray = false;
    protected $tempDir = null;

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->type('file');
        $this->attr('multiple', true);
        /* add validation to throw messages from PHP's errors */
        $this->addValidatorFunction(
            'phpfileerrors',
            function (&$field) {
                foreach ($field->value() as $file) {
                    if ($e = $file['error']) {
                        switch ($e) {
                        case 1:
                            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                        case 2:
                            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form. ';
                        case 3:
                            return 'The uploaded file was only partially uploaded.';
                        case 6:
                            return 'Missing a temporary folder.';
                        case 7:
                            return 'Failed to write file to disk.';
                        case 8:
                            return 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.';
                        }
                    }
                }
                return true;
            }
        );
    }

    public function isFilled()
    {
        return !!$this->value();
    }

    /**
     * Override parent-setting to set enctype of root form
     */
    public function &parent(FieldInterface &$set=null) : ?FieldInterface
    {
        if ($set) {
            $set->rootParent()->attr('enctype', 'multipart/form-data');
        }
        return parent::parent($set);
    }

    public function tempDir($set = null)
    {
        if ($set !== null) {
            $this->tempDir = $set;
        }
        if ($this->tempDir) {
            return $this->tempDir;
        }
        return \sys_get_temp_dir();
    }

    protected function tempFile()
    {
        return tempnam(
            $this->tempDir(),
            'File'
        );
    }

    /**
     * Override submittedValue because these values live in $_FILES
     * Also assemble the structure of $_FILES into an array where the first
     * dimension is the index and second is the keys.
     */
    public function submittedValue()
    {
        if ($this->fileArray === false) {
            $this->fileArray = [];
            if (isset($_FILES[$this->name()])) {
                foreach ($_FILES[$this->name()] as $key => $values) {
                    foreach ($values as $i => $value) {
                        @$this->fileArray[$i][$key] = $value;
                    }
                }
                $this->fileArray = array_filter(
                    $this->fileArray,
                    function ($e) {
                        return $e['error'] != 4;
                    }
                );
                foreach ($this->fileArray as $i => $file) {
                    $tempFile = $this->tempFile();
                    move_uploaded_file($file['tmp_name'], $tempFile);
                    $this->fileArray[$i]['file'] = $tempFile;
                    unset($this->fileArray[$i]['tmp_name']);
                }
            }
        }
        return $this->fileArray;
    }

    /**
     * File fields never have an HTML value
     */
    public function htmlValue()
    {
        return null;
    }


    protected function htmlAttributes()
    {
        $attr = parent::htmlAttributes();
        $attr['name'] = $this->name().'[]';
        return $attr;
    }
}
