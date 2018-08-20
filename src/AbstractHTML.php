<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward;

abstract class AbstractHTML
{
    protected $classes = array();
    protected $attributes = array();

    abstract protected function htmlTag();
    abstract protected function htmlTagSelfClosing();
    abstract protected function htmlTagContent();

    public function attr(string $name, string $value = null) : ?string
    {
        if ($value === true) {
            $this->attributes[$name] = true;
        } elseif ($value === false) {
            unset($this->attributes[$name]);
            return null;
        }
        if ($value !== null) {
            $this->attributes[$name] = $value;
        }
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return null;
    }

    protected function htmlAttributes()
    {
        $out = $this->attributes;
        if ($this->classes()) {
            $out['class'] = implode(' ', $this->classes());
        }
        return $out;
    }

    public function addClass(string $class)
    {
        $this->classes[] = $class;
        $this->classes = array_unique($this->classes);
        asort($this->classes);
    }

    public function removeClass(string $class)
    {
        $this->classes = array_filter(
            $this->classes,
            function ($e) use ($class) {
                return $e != $class;
            }
        );
    }

    public function classes(string $prefix = null) : array
    {
        return array_map(function ($i) use ($prefix) {
            return $prefix.$i;
        }, $this->classes);
    }

    protected function buildHtmlAttributes(array $attributes) : string
    {
        if (!$attributes) {
            return '';
        }
        $out = array();
        foreach ($attributes as $key => $value) {
            if ($value === false || $value === null) {
                continue;
            } elseif ($value === true) {
                $out[] = "$key";
            } else {
                $out[] = "$key=\"$value\"";
            }
        }
        return ' '.implode(' ', $out);
    }

    public function buildHtmlTag(string $tag, bool $selfClosing, array $attributes, string $content=null)
    {
        $out = array();
        $out[] = '<'.$tag;
        $out[] = $this->buildHtmlAttributes($attributes);
        $out[] = ($selfClosing?'/':'').'>';
        if (!$selfClosing) {
            $out[] = $content.'</'.$tag.'>';
        }
        return implode('', $out);
    }

    public function __toString()
    {
        return $this->buildHtmlTag(
            $this->htmlTag(),
            $this->htmlTagSelfClosing(),
            $this->htmlAttributes(),
            $this->htmlTagContent()
        );
    }
}
