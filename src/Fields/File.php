<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\FieldInterface;

class File extends Input
{
    protected $fileArray = false;
    protected $storageLocation = null;

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
     * Moves the uploaded file to a particular directory, with its own unique
     * filename. Filename is based on uniqid, and the value is updated if
     * necessary.
     */
    public function moveFileTo($targetDir, $copy=false)
    {
        //make sure target exists and is writeable
        $targetDir = realpath($targetDir);
        //make sure current file exists (we need its uniqid)
        if (!($value = $this->value())) {
            return false;
        }
        //set up unique target file in directory
        $i = 0;
        do {
            $target = $targetDir.'/'.$value['uniqid'].($i?"_$i":'');
            $i++;
        } while (file_exists($target));
        //move file
        return $this->moveFile($target, null, $copy);
    }

    /**
     * Moves the uploaded file to another filename, updates value as needed
     */
    public function moveFile($target, $force=false, $copy=false)
    {
        //never overwrite without $force
        if (!is_file($target) || ($force && is_writeable($target))) {
            //make sure current file is specified
            if ($current = @$this->value()['file']) {
                //make sure current file exists
                if (!is_file($current)) {
                    return false;
                }
                //move/copy file
                if (is_uploaded_file($current)) {
                    //uploaded files ignore $copy                    return move_uploaded_file($current, $target) && $this->fileArray['file'] = realpath($target);
                } else {
                    if ($copy) {
                        return copy($current, $target) && $this->fileArray['file'] = realpath($target);
                    } else {
                        return rename($current, $target) && $this->fileArray['file'] = realpath($target);
                    }
                }
            }
        }
        return false;
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

    /**
     * Override submittedValue because these values live in $_FILES
     */
    public function submittedValue()
    {
        if ($this->fileArray === false) {
            if (isset($_FILES[$this->name()]) && !$_FILES[$this->name()]['error']) {
                $this->fileArray = [
                    'name' => $_FILES[$this->name()]['name'],
                    'type' => $_FILES[$this->name()]['type'],
                    'file' => $_FILES[$this->name()]['tmp_name'],
                    'size' => $_FILES[$this->name()]['size'],
                    'uniqid' => uniqid()
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
