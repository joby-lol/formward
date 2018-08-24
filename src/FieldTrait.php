<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward;

trait FieldTrait
{
    protected $parent;
    protected $tips = array();
    protected $value;
    protected $required;
    protected $default;
    protected $label;
    protected $method = 'post';
    protected $name;

    public function addTip($tip, $name)
    {
        if (!$name) {
            $name = md5($tip);
        }
        $this->tips[$name] = $tip;
        return $name;
    }

    public function htmlTips() : string
    {
        if ($this->tips) {
            $out[] = '<div class="tips">';
            foreach ($this->tips as $key => $value) {
                $out[] = '<div class="tip tip-'.$key.'">'.$value.'</div>';
            }
            $out[] = '</div>';
            return implode(PHP_EOL, $out);
        }
        return '';
    }

    public function containerMayWrap() : bool
    {
        return true;
    }

    public function wrapperContentOrder() : array
    {
        return array(
            '{label}',
            '{field}',
            '{tips}'
        );
    }

    /**
     * Pull the submitted value of this field by looking for it's value in
     * either $_GET or $_SET, indexed by its name. Which is used is determined
     * by method()
     */
    public function submittedValue()
    {
        if ($this->method() == 'get') {
            return @$_GET[$this->name()];
        }
        return @$_POST[$this->name()];
    }

    /**
     * get/set this field's "current" value.
     *
     * The current value is one of (in order of precedence)
     * * value set via value()
     * * submittedValue()
     * * default()
     * * null
     */
    public function value($set = null)
    {
        if ($set) {
            $this->value = $set;
        }
        if ($this->value !== null) {
            return $this->value;
        }
        if ($this->submittedValue() !== null) {
            return $this->submittedValue();
        }
        if ($this->default() !== null) {
            return $this->default();
        }
        return null;
    }

    /**
     * get/set whether this field is required
     */
    public function required($set = null)
    {
        if ($set !== null) {
            if ($set) {
                $this->addValidator('required', new Validators\Required());
            }
            $this->required = $set;
        }
        return $this->required;
    }

    /**
     * get/set default value
     */
    public function default($set = null)
    {
        if ($set) {
            $this->default = $set;
        }
        return $this->default;
    }

    /**
     * get/set the user-facing label for this field
     */
    public function label($set = null)
    {
        if ($set) {
            $this->label = $set;
        }
        return $this->label;
    }

    /**
     * get/set the method to use for this field
     * Generally nobody should do this on individual fields, but Containers
     * propagate it down into their children.
     */
    public function method($set = null)
    {
        //if there is a parent, use its method and ignore ours
        if ($this->parent()) {
            return $this->parent()->method();
        }
        //otherwise set method to either get or post
        if ($set !== null) {
            if ($set != 'get') {
                $set = 'post';
            }
        }
        if ($set) {
            $this->method = $set;
        }
        return $this->method;
    }

    /**
     * Get/set the parent object of this field
     */
    public function &parent(FieldInterface &$set=null) : ?FieldInterface
    {
        if ($set !== null) {
            $this->parent = $set;
        }
        return $this->parent;
    }

    /**
     * Return the attributes that a field should have. This function may need
     * overriding in some cases.
     */
    protected function fieldAttributes()
    {
        $out = array(
            'id' => $this->name(),
            'name' => $this->name()
        );
        if ($this->validated() === true) {
            $out['data-validation'] = "valid";
        } elseif ($this->validated() === false) {
            $out['data-validation'] = "invalid";
        }
        return $out;
    }

    /**
     * Set or get the current name of this field. When getting the name, a
     * fully qualified name is returned, prepending all current parent names.
     */
    public function name($set = null)
    {
        if ($set !== null) {
            $this->name = $this->sanitizeName($set);
        }
        return $this->prependParentName($this->name);
    }

    /**
     * Make sure a given name is valid, making changes to make it valid if that
     * is necessary/possible
     */
    protected function sanitizeName(string $name) : string
    {
        $name = str_replace('_', '-', $name);
        $name = preg_replace('/[^a-z0-9\-]/i', '', $name);
        //TODO: more validation
        return $name;
    }

    /**
     * Recursively build the fully qualified name of this object by asking its
     * parent what its name is
     */
    protected function prependParentName(string $name) : string
    {
        if ($this->parent() !== null) {
            return $this->parent()->name().'_'.$name;
        }
        return $name;
    }
}
