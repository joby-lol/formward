<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;

class Select extends AbstractField
{
    protected $options = [];

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->attr('type', 'text');
    }

    public function options(array $set = null) : ?array
    {
        if ($set) {
            $this->options = $set;
        }
        return $this->options;
    }

    protected function htmlTagContent() : ?string
    {
        $opts = [];
        if (true || !$this->required()) {
            $opts[] = '<option value="">-- none --</option>';
        }
        if ($this->options()) {
            foreach ($this->options() as $key => $value) {
                $opt = '<option';
                $opt .=  ' value="'.htmlspecialchars($key).'"';
                $opt .= '>'.htmlspecialchars($value).'</option>';
                $opts[] = $opt;
            }
        } else {
            $this->tip('Configuration problem: no options specified');
        }
        return implode(PHP_EOL, $opts);
    }

    protected function htmlTag()
    {
        return 'select';
    }

    protected function htmlTagSelfClosing()
    {
        return false;
    }
}
