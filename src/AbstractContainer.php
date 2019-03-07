<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward;

use Flatrr\FlatArrayTrait;

abstract class AbstractContainer extends AbstractField implements ContainerInterface
{
    use FlatArrayTrait;

    public $tag = 'div';
    public $selfClosing = false;
    public $wrapContainerItems = true;

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        parent::__construct($label, $name, $parent);
        $this->removeClass('Field');
        $this->addClass('Container');
    }

    public function set(?string $name, $value)
    {
        if ($value) {
            $value->parent($this);
            $value->name($name);
        }
        //this is from FlatArrayTrait
        return $this->flattenSearch($name, $value);
    }

    public function default($default = null)
    {
        return $this->recurse('default', $default);
    }

    public function value($value = null)
    {
        return $this->recurse('value', $value);
    }

    public function submittedValue()
    {
        return $this->recurse('submittedValue');
    }

    public function validationMessage($set = null)
    {
        $out = array();
        if (parent::validationMessage($set)) {
            $out[$this->name()] = parent::validationMessage();
        }
        foreach ($this->recurse('validationMessage') as $key => $value) {
            if ($value) {
                $out[$this[$key]->name()] = $value;
            }
        }
        return $this->flatten($out);
    }

    protected function flatten($a)
    {
        foreach ($a as $ak => $b) {
            if (is_array($b)) {
                unset($a[$ak]);
                foreach ($this->flatten($b) as $bk => $bv) {
                    $a[$bk] = $bv;
                }
            }
        }
        return $a;
    }

    /**
     * Add data-validation attribute so that validation states propagate upwards
     */
    protected function htmlAttributes()
    {
        $attr = parent::htmlAttributes();
        if ($this->validated() === true) {
            $attr['data-validation'] = 'valid';
        } elseif ($this->validated() === false) {
            $attr['data-validation'] = 'invalid';
        }
        return $attr;
    }

    /**
     * Containers can still use Validator objects or validation functions,
     * but be warned that most of the ones for fields won't work right on
     * a container.
     *
     * Containers also validate their child fields recursively.
     */
    public function validate() : bool
    {
        $valid = true;
        //first call parent::validate() to execute any attached Validator objects of functions
        if (parent::validate() === false) {
            $valid = false;
        }
        //then do our children's validation
        foreach ($this as $item) {
            if (!$item->validate()) {
                $valid = false;
            }
        }
        //return validated status
        return $this->validated($valid);
    }

    /**
     * Recursively call a method on all child fields, and assemble the results
     * into an array.
     */
    protected function recurse(string $method, array $value = null) : array
    {
        $out = array();
        foreach ($this as $offset => $item) {
            $out[$offset] = $item->$method($value?@$value[$offset]:null);
        }
        return $out;
    }

    protected function recursiveSet(string $method, $value) : void
    {
        foreach ($this as $item) {
            $item->$method($value);
        }
    }

    /**
     * Set whether or not to wrap container items -- if set to false just bare
     * field inputs are used.
     */
    public function wrapContainerItems(bool $set = null) : bool
    {
        if ($set !== null) {
            $this->wrapContainerItems = $set;
        }
        return $this->wrapContainerItems;
    }

    /**
     * Convert an item into a string for this container's markup output
     */
    protected function containerItemHtml($item)
    {
        if ($this->wrapContainerItems && $item->containerMayWrap()) {
            $out = array();
            $validation='';
            if ($item->validated() === true) {
                $validation = 'data-validation="valid" ';
            } elseif ($item->validated() === false) {
                $validation = 'data-validation="invalid" ';
            }
            $out[] = '<div '.$validation.'id="_wrapper_'.$item->name().'" class="FieldWrapper '.implode(' ', $item->classes('FieldWrapper-')).'">';
            foreach ($item->wrapperContentOrder() as $c) {
                switch ($c) {
                    case '{field}': $out[] = "$item"; break;
                    case '{label}': $out[] = '<label for="'.$item->name().'">'.$item->label().'</label>'; break;
                    case '{tips}': $out[] = $item->htmlTips(); break;
                    default: $out[] = $c; break;
                }
            }
            $out[] = '</div>';
            return implode(PHP_EOL, $out);
        }
        return "$item";
    }

    /**
     * HTML tag content is a list of all the elements in this container
     */
    protected function htmlContent() : ?string
    {
        $out = '<label>'.$this->label().'</label>';
        if ($this->wrapContainerItems) {
            $out .= $this->htmlTips();
        }
        $out .= PHP_EOL.implode(PHP_EOL, array_map(
            function ($item) {
                return $this->containerItemHtml($item);
            },
            $this->get()
        )).PHP_EOL;
        if (!$this->wrapContainerItems) {
            $out .= $this->htmlTips();
        }
        return $out;
    }

    public function containerMayWrap() : bool
    {
        return false;
    }
}
