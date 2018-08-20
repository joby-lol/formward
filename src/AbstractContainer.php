<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward;

abstract class AbstractContainer extends AbstractField implements ContainerInterface, \ArrayAccess, \Iterator
{
    protected $classes = array('Container');

    private $arrayAccessData = array();
    private $iteratorArrayMap = array();
    private $changed = false;
    private $iterPos;


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

    public function validationMessage(string $set = null)
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
                foreach ($this->flatten($b) as $bk => $bv) {
                    $a[$bk] = $bv;
                }
            }
        }
        return $a;
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
        //first call parent::validate() to execute any attached Validator objects of functions
        if (parent::validate() === false) {
            $this->validated(false);
        } else {
            //unless any errors come up we'll start by assuming everything is valid
            $this->validated(true);
        }
        //then do our children's validation
        foreach ($this->arrayAccessData as $offset => $item) {
            if (!$item->validate()) {
                $this->validated(false);
            }
        }
        //return validated status
        return $this->validated();
    }

    /**
     * Recursively call a method on all child fields, and assemble the results
     * into an array.
     */
    protected function recurse(string $method, array $value = null) : array
    {
        $out = array();
        foreach ($this->arrayAccessData as $offset => $item) {
            $out[$offset] = $item->$method($value?$value[$offset]:null);
        }
        return $out;
    }

    protected function recursiveSet(string $method, $value) : void
    {
        foreach ($this->arrayAccessData as $offset => $item) {
            $out[$offset] = $item->$method($value);
        }
    }

    /**
     * Whenever my method is changed, also change my children
     */
    public function method(string $method = null) : string
    {
        $return = parent::method($method);
        foreach ($this->arrayAccessData as $item) {
            $item->method(parent::method($method));
        }
        return $return;
    }

    /**
     * Convert an item into a string for this container's markup output
     */
    protected function containerItemHtml($item)
    {
        if ($item->containerMayWrap()) {
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
    protected function htmlTagContent() : ?string
    {
        return PHP_EOL.implode(PHP_EOL, array_map(
            function ($item) {
                return $this->containerItemHtml($item);
            },
            $this->arrayAccessData
        )).PHP_EOL;
    }

    protected function htmlTag()
    {
        return 'div';
    }

    protected function htmlTagSelfClosing()
    {
        return false;
    }

    /**
     * Rebuild the map of what order keys should be iterated over.
     */
    private function buildIterMap()
    {
        $this->iteratorArrayMap = array();
        foreach ($this->arrayAccessData as $key => $value) {
            $this->iteratorArrayMap[] = $key;
        }
    }

    /**
     * Add an item to the front of the form
     */
    public function unshift($offset, $value)
    {
        $value->parent($this);
        $value->name($offset);
        $value->method($this->method());
        $this->arrayAccessData = array_merge(
            array($offset,$value),
            $this->arrayAccessData
        );
        $this->buildIterMap();
    }

    /**
     * Inherited from \ArrayAccess
     */
    public function offsetSet($offset, $value)
    {
        $value->parent($this);
        $value->name($offset);
        $value->method($this->method());
        $this->arrayAccessData[$offset] = $value;
        $this->buildIterMap();
    }

    /**
     * Inherited from \ArrayAccess
     */
    public function offsetExists($offset)
    {
        return isset($this->arrayAccessData[$offset]);
    }

    /**
     * Inherited from \ArrayAccess
     */
    public function offsetUnset($offset)
    {
        if (isset($this->arrayAccessData[$offset])) {
            $this->setChanged(true);
        }
        unset($this->arrayAccessData[$offset]);
        $this->buildIterMap();
    }

    /**
     * Return a reference to an item in arrayAccessData
     */
    protected function &getRef($offset)
    {
        return $this->arrayAccessData[$offset];
    }

    /**
     * Inherited from \ArrayAccess
     */
    public function offsetGet($offset)
    {
        if (isset($this->arrayAccessData[$offset])) {
            return $this->getRef($offset);
        }
        return null;
    }

    /**
     * Inherited from \Iterator
     */
    public function rewind()
    {
        $this->iterPos = 0;
    }

    /**
     * Inherited from \Iterator
     */
    public function &current()
    {
        if (isset($this->arrayAccessData[$this->key()])) {
            return $this->arrayAccessData[$this->key()];
        }
        $return = null;
        return $return;
    }

    /**
     * Inherited from \Iterator
     */
    public function key()
    {
        return isset($this->iteratorArrayMap[$this->iterPos]) ? $this->iteratorArrayMap[$this->iterPos] : null;
    }

    /**
     * Inherited from \Iterator
     */
    public function next()
    {
        $this->iterPos++;
    }

    /**
     * Inherited from \Iterator
     */
    public function valid()
    {
        return isset($this->arrayAccessData[$this->key()]);
    }
}
