<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\SystemFields;

use Sesh\SessionTrait;

class Token extends AbstractSystemField implements TokenInterface
{
    use SessionTrait;

    protected $init = false;

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
        $this->sessionTokenCleanup(true);
    }

    /**
     * Always return a CSRF token. This field disregards default/submittedValue
     */
    public function value($value = null)
    {
        if (!$this->init) {
            $this->sessionTraitInit($this->name());
            $this->init = true;
        }
        return $this->sessionGetToken('csrf');
    }
}
