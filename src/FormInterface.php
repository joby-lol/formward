<?php
/* Formward | https://github.com/jobyone/formward | MIT License */

namespace Formward;

interface FormInterface
{
    public function submitted(): bool;
    public function handle(callable $validFn = null, callable $invalidFn = null, callable $notSubmittedFn = null): ?bool;
    public function oneTimeTokens(bool $set = null): bool;
    public function csrf(bool $set = null): bool;
    public function tokenName(): string;
    public function tokenValue(): string;
    public function &submitButton();
    public function action(string $action = null): ?string;
}
