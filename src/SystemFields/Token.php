<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\SystemFields;

use Sesh\SessionTrait;

class Token extends AbstractSystemField implements TokenInterface
{
    use SessionTrait;

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
        $this->sessionTokenCleanup(true);
    }

    /**
     * Always return a CSRF token. This field disregards default/submittedValue
     */
    public function value($value = null)
    {
        $this->sessionTraitInit($this->name());
        return $this->sessionGetToken('csrf');
    }
}
