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

    protected $action = '#';
    protected $submitted = null;
    protected $oneTimeTokens = true;
    protected $csrf = true;

    public $tag = 'form';

    public function __construct(string $label, string $name=null, FieldInterface $parent=null)
    {
        if (!$name) {
            $name = md5(__FILE__.'/'.$label);
        }
        parent::__construct($label, $name, $parent);
        $this->addClass('Form');
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
    public function action(string $set = null) : string
    {
        if ($set !== null) {
            $this->action = $set;
        }
        return $this->action;
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
        if ($this->submitted === null) {
            $this->submitted = $this->systemFields['token']->test();
            if ($this->submitted && $this->oneTimeTokens()) {
                $this->systemFields['token']->clear();
            }
        }
        return $this->submitted;
    }

    public function oneTimeTokens(bool $set = null) : bool
    {
        if ($set !== null) {
            $this->oneTimeTokens = $set;
        }
        return $this->oneTimeTokens;
    }

    public function csrf(bool $set = null) : bool
    {
        if ($set !== null) {
            $this->csrf = $set;
            $this->setupToken();
        }
        return $this->csrf;
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
    protected function htmlContent() : ?string
    {
        //basic output
        $out = [
            $this->validationMessagesHTML(),
            parent::htmlContent()
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
    protected function htmlAttributes()
    {
        $attr = parent::htmlAttributes();
        $attr['method'] = $this->method();
        $attr['action'] = $this->action();
        return $attr;
    }

    protected function htmlTag()
    {
        return 'form';
    }

    public function __toString()
    {
        if ($this->submitted()) {
            $this->validate();
        }
        return parent::__toString();
    }
}
