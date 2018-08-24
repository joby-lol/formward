<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward;

use HtmlObjectStrings\GenericTag;
use HtmlObjectStrings\TagInterface;

abstract class AbstractField extends GenericTag implements FieldInterface, TagInterface
{
    use Validators\ValidatorTrait, FieldTrait;

    public $tag = 'input';
    public $selfClosing = true;

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        if (!$name) {
            $name = $label;
        }
        $this->label($label);
        $this->name($name);
        $this->parent($parent);
        $this->addClass('Field');
        $class = explode('\\', get_called_class());
        $class = array_pop($class);
        $this->addClass('class-'.$class);
    }

    protected function htmlAttributes()
    {
        $attr = parent::htmlAttributes();
        $attr['id'] = $this->name();
        $attr['name'] = $this->name();
        return $attr;
    }

    public function classes($prefix = null) : array
    {
        return array_map(
            function ($e) use ($prefix) {
                return $prefix.$e;
            },
            parent::classes()
        );
    }
}
