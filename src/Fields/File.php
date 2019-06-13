<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class File extends Input
{
    protected $fileArray = false;
    protected $tempDir = null;

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->type('file');
        /* add validation to throw messages from PHP's errors */
        $this->addValidatorFunction(
            'phpfileerrors',
            function (&$field) {
                if ($a = @$_FILES[$field->name()]) {
                    if ($e = $a['error']) {
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
            'FileMulti'
        );
    }

    /**
     * Override submittedValue because these values live in $_FILES
     */
    public function submittedValue()
    {
        if ($this->fileArray === false) {
            if (isset($_FILES[$this->name()]) && !$_FILES[$this->name()]['error']) {
                $tempFile = $this->tempFile();
                move_uploaded_file($_FILES[$this->name()]['tmp_name'], $tempFile);
                $this->fileArray = [
                    'name' => $_FILES[$this->name()]['name'],
                    'type' => $_FILES[$this->name()]['type'],
                    'file' => $tempFile,
                    'size' => $_FILES[$this->name()]['size']
                ];
            } else {
                $this->fileArray = null;
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
}
