<?php

namespace App\Utils\Checker;

use App\Utils\Misc;
use App\Models\User;

class FormChecker {

    private array $required_set = [];
    private array $rule_set = [];
    private array $messages = [];

    public function __construct(array $required_set = []) {
        $this->required_set = $required_set;
    }

    public static function formUploaded(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function setRules(array $rule_set = []) {
        $this->rule_set = $rule_set;
    }

    public function addRule(array $rule) {
        if (Misc::isList($rule) && count($rule) == 3) {
            $rule_to_add = [$rule[0] => [$rule[1], $rule[2]]];
            $this->rule_set[$rule[0]][$rule[1]] = $rule[2];
        }
    }

    public function verifyExists() {
        foreach ($this->required_set as $element) {
            // Vérifie si l'élément existe et n'est pas vide
            if (isset($_POST[$element]) && trim($_POST[$element]) == '') {
                $this->messages[$element] = ["Le champ '$element' est requis."];
            }
        }
    }

    public function check() : bool {
        $rule_success = true;
        $this->verifyExists();
        if (count($this->messages) > 0) {
            $rule_success = false;
        }
        foreach ($this->rule_set as $rule_element => $rules) {
            foreach ($rules as $rule => $value) {
                switch ($rule) {
                    case "min-length":
                        if (strlen($_POST[$rule_element]) < $value) {
                            $rule_success = false;
                            if (isset($this->messages[$rule_element])) {
                                array_push($this->messages[$rule_element], '$lang::forms/messages/'.$rule_element.'/min-length');
                            } else {
                                $this->messages[$rule_element] = ['$lang::forms/messages/'.$rule_element.'/min-length'];
                            }
                        }
                        break;
                    case "max-length":
                        if (strlen($_POST[$rule_element]) > $value) {
                            $rule_success = false;
                            if (isset($this->messages[$rule_element])) {
                                array_push($this->messages[$rule_element], '$lang::forms/messages/'.$rule_element.'/max-length');
                            } else {
                                $this->messages[$rule_element] = ['$lang::forms/messages/'.$rule_element.'/max-length'];
                            }
                        }
                        break;
                    case "lcChars":
                        if (!preg_match('/[a-z]/', $_POST[$rule_element])) {
                            $rule_success = false;
                            if (isset($this->messages[$rule_element])) {
                                array_push($this->messages[$rule_element], '$lang::forms/messages/needs-lowercases');
                            } else {
                                $this->messages[$rule_element] = ['$lang::forms/messages/needs-lowercases'];
                            }
                        }
                        break;
                    case "ucChars":
                        if (!preg_match('/[A-Z]/', $_POST[$rule_element])) {
                            $rule_success = false;
                            if (isset($this->messages[$rule_element])) {
                                array_push($this->messages[$rule_element], '$lang::forms/messages/needs-uppercases');
                            } else {
                                $this->messages[$rule_element] = ['$lang::forms/messages/needs-uppercases'];
                            }
                        }
                        break;
                    case "symbolChars":
                        if (!preg_match('/[^a-zA-Z0-9]/', $_POST[$rule_element])) {
                            $rule_success = false;
                            if (isset($this->messages[$rule_element])) {
                                array_push($this->messages[$rule_element], '$lang::forms/messages/needs-special');
                            } else {
                                $this->messages[$rule_element] = ['$lang::forms/messages/needs-special'];
                            }
                        }
                        break;
                    case "equals-input":
                        if ($rule_element != $value[1]) {
                            if (isset($this->messages[$rule_element])) {
                                array_push($this->messages[$rule_element], '$lang::forms/messages/'.$rule_element.'/do-not-correspond');
                            } else {
                                $this->messages[$rule_element] = ['$lang::forms/messages/'.$rule_element.'/do-not-correspond'];
                            }
                        }
                        break;
                    case "db-exists":
                        $classname = "App\\Models\\" . ucfirst($value[0]);
                        $class = new $classname();
                        if ($class->rowExist($rule_element, $_POST[$rule_element]) != $value[1]) {
                            $rule_success = false;
                            if ($value[1] == true) {
                                if (isset($this->messages[$rule_element])) {
                                    array_push($this->messages[$rule_element], '$lang::forms/messages/'.$rule_element.'/do-not-exists');
                                } else {
                                    $this->messages[$rule_element] = ['$lang::forms/messages/'.$rule_element.'/do-not-exists'];
                                }
                            } else {
                                if (isset($this->messages[$rule_element])) {
                                    array_push($this->messages[$rule_element], '$lang::forms/messages/'.$rule_element.'/already-exists');
                                } else {
                                    $this->messages[$rule_element] = ['$lang::forms/messages/'.$rule_element.'/already-exists'];
                                }
                            }
                        }
                        break;
                }
            }
        }
        return $rule_success;
    }

    public function getMessages() {
        return $this->messages;
    }
    
}