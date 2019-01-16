<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

use Formward\AbstractField;
use Formward\FieldInterface;

class Select extends AbstractField
{
    protected $options = [];

    public $tag = 'select';
    public $selfClosing = false;
    public $nullText = '-- none --';

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->attr('type', 'text');
    }

    public function value($set = null)
    {
        $value = parent::value($set);
        if (!isset($this->options[$value])) {
            $value = null;
        }
        return $value;
    }

    public function options(array $set = null) : ?array
    {
        if ($set) {
            $this->options = $set;
        }
        return $this->options;
    }

    protected function htmlAttributes()
    {
        $attr = parent::htmlAttributes();
        if ($this->required()) {
            $attr['required'] = 'true';
        }
        if ($this->disabled()) {
            $attr['disabled'] = true;
        }
        return $attr;
    }

    protected function htmlContent() : ?string
    {
        $opts = [];
        if (!$this->required()) {
            $opts[] = '<option value="">'.$this->nullText.'</option>';
        }
        if ($this->options()) {
            foreach ($this->options() as $key => $value) {
                $opt = '<option';
                $opt .=  ' value="'.htmlspecialchars($key).'"';
                if ($key == $this->value()) {
                    $selected = true;
                    if ($this->value() === true || $this->value() === false || $this->value() === 0 || $this->value() === null) {
                        $selected = ($key === $this->value());
                    }
                    if ($selected) {
                        $opt .= ' selected';
                    }
                }
                $opt .= '>'.htmlspecialchars($value).'</option>';
                $opts[] = $opt;
            }
        } else {
            $this->addTip('Configuration problem: no options specified');
        }
        return implode(PHP_EOL, $opts);
    }
}
