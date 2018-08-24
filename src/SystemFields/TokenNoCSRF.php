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

    /**
     * Check a value against my token
     */
    public function test(?string $token) : bool
    {
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
