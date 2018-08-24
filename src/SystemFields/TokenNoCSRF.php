<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\SystemFields;

use Digraph\Session\SessionTrait;

class TokenNoCSRF extends AbstractSystemField implements TokenInterface
{
    public function containerMayWrap() : bool
    {
        return false;
    }

    protected function htmlAttributes()
    {
        $attr = parent::htmlAttributes();
        if ($value = $this->value()) {
            $attr['value'] = $value;
        }
        return $attr;
    }

    /**
     * Check a value against my token
     */
    public function test(?string $token = null) : bool
    {
        if (!$token) {
            $token = $this->submittedValue();
        }
        return $token == $this->value();
    }

    /**
     * Clear my token
     */
    public function clear() : void
    {
        //intentionally does nothing
    }

    /**
     * Always return a CSRF token. This field disregards default/submittedValue
     */
    public function value($value = null)
    {
        return '1';
    }
}
