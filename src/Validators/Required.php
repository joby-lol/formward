<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward\Validators;

use Formward\ContainerInterface;

class Required extends AbstractValidator
{
    public function validate()
    {
        if ($this->field instanceof ContainerInterface) {
            return $this->validateContainer();
        } else {
            return $this->validateField();
        }
    }

    public function validateContainer()
    {
        // return $this->validate_helper($this->field);
        if (!$this->validate_helper($this->field)) {
            $this->message = 'The section "'.$this->field->label().'" is required';
            return false;
        }
        return true;
    }

    protected function validate_helper($field)
    {
        if (!$field->isFilled()) {
            return false;
        }
        if ($field instanceof ContainerInterface) {
            foreach ($field as $child) {
                if ($child->required() && !$this->validate_helper($child)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function validateField()
    {
        if (!$this->validate_helper($this->field)) {
            $this->message = 'The field "'.$this->field->label().'" is required';
            return false;
        }
        return true;
    }
}
