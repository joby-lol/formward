<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\SystemFields;

interface TokenInterface
{
    public function test(?string $token) : bool;
    public function clear() : void;
}
