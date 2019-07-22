<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Fields;

class CheckboxList extends Container
{
    protected $options = [];

    public function options(array $set = null) : ?array
    {
        if ($set) {
            $this->options = $set;
            $this->buildOptions();
        }
        return $this->options;
    }

    public function value($set = null)
    {
        parent::value($this->set_conv($set));
        return $this->get_conv(parent::value());
    }

    public function default($set = null)
    {
        parent::default($this->set_conv($set));
        return $this->get_conv(parent::default());
    }

    protected function get_conv($get)
    {
        $out = [];
        foreach ($this->options as $k => $n) {
            if ($this[md5($k)] && $this[md5($k)]->value()) {
                $out[] = $k;
            }
        }
        return $out;
    }

    protected function set_conv($set)
    {
        $out = [];
        if (is_array($set)) {
            foreach ($set as $k => $v) {
                $out[md5($k)] = $v;
            }
        }
        return $out;
    }

    protected function buildOptions()
    {
        //reset option fields
        $this->set(null, []);
        //add checkboxes
        foreach ($this->options() as $k => $v) {
            $this[md5($k)] = new Checkbox($v);
        }
    }
}
