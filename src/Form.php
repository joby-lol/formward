<?php
/* Formward | https://gitlab.com/byjoby/formward | MIT License */
namespace Formward;

class Form extends AbstractContainer implements FormInterface
{
    protected $systemFields = array();
    protected $classes = array('Form');

    public $validFn;
    public $invalidFn;
    public $notSubmittedFn;

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        if (!$name) {
            $name = md5(__FILE__.'/'.$label);
        }
        parent::__construct($label, $name, $parent);
        $this->systemFields['submit'] = new SystemFields\Submit('Submit form', 'submit', $this);
        $this->setupToken();
    }

    public function &submitButton()
    {
        return $this->systemFields['submit'];
    }

    /**
     * get/set the action to use for this form
     */
    public function action(string $action = null) : string
    {
        return $this->valueFunction('action', $action, '#');
    }

    protected function setupToken()
    {
        if ($this->csrf() === false) {
            $this->systemFields['token'] = new SystemFields\TokenNoCSRF('Token field', 'token', $this);
        } else {
            $this->systemFields['token'] = new SystemFields\Token('Token field', 'token', $this);
        }
    }

    public function handle(callable $validFn = null, callable $invalidFn = null, callable $notSubmittedFn = null) : ?bool
    {
        $this->validFn = $validFn;
        $this->invalidFn = $invalidFn;
        $this->notSubmittedFn = $notSubmittedFn;
        if ($this->submitted()) {
            if ($this->validate()) {
                if ($this->validFn) {
                    ($this->validFn)($this);
                }
                return true;
            }
            if ($this->invalidFn) {
                ($this->invalidFn)($this);
            }
            return false;
        }
        if ($this->notSubmittedFn) {
            ($this->notSubmittedFn)($this);
        }
        return null;
    }

    public function submitted() : bool
    {
        if ($this->valueFunction('submitted') === null) {
            $this->valueFunction('submitted', $this->systemFields['token']->test(
                $this->systemFields['token']->submittedValue()
            ));
            if ($this->valueFunction('submitted') && $this->oneTimeTokens()) {
                $this->systemFields['token']->clear();
            }
        }
        return $this->valueFunction('submitted');
    }

    public function oneTimeTokens(bool $set = null) : bool
    {
        return $this->valueFunction('oneTimeTokens', $set, true);
    }

    public function csrf(bool $set = null) : bool
    {
        $out = $this->valueFunction('csrf', $set, true);
        if ($set !== null) {
            $this->setupToken();
        }
        return $out;
    }

    public function tokenName() : string
    {
        return $this->systemFields['token']->name();
    }

    public function tokenValue() : string
    {
        return $this->systemFields['token']->value();
    }

    public function &parent(FieldInterface &$parent = null) : ?FieldInterface
    {
        if ($parent !== null) {
            throw new \Exception("Top-level forms can't be nested in other Containers");
        }
        return parent::parent($parent);
    }

    protected function validationMessagesHTML()
    {
        $out = [];
        if ($this->validationMessage()) {
            $out[] = '<div class="validation-messages">';
            foreach ($this->validationMessage() as $id => $message) {
                $out[] = '<div class="validation-message">';
                $out[] = "<a href=\"#_wrapper_$id\">";
                $out[] = $message;
                $out[] = '</a>';
                $out[] = '</div>';
            }
            $out[] = '</div>';
        }
        return implode(PHP_EOL, $out);
    }

    /**
     * Add system fields to html tag content
     */
    protected function htmlTagContent() : ?string
    {
        //basic output
        $out = [
            $this->validationMessagesHTML(),
            parent::htmlTagContent()
        ];
        //add system fields if necessary
        if ($this->systemFields) {
            $out[] = implode(
                PHP_EOL,
                array_map(
                    function ($i) {
                        return $this->containerItemHtml($i);
                    },
                    $this->systemFields
                )
            );
        }
        return implode(PHP_EOL, $out);
    }

    /**
     * Return the attributes that a field should have. This function may need
     * overriding in some cases.
     */
    protected function fieldAttributes()
    {
        $out = parent::fieldAttributes();
        $out['method'] = $this->method();
        $out['action'] = $this->action();
        return $out;
    }

    protected function htmlTag()
    {
        return 'form';
    }
}
