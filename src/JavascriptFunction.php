<?php

namespace Rasba;

class JavascriptFunction
{
    public $Code = '';
    public $RasbaJs;
    public $Document;

    public function __construct($RasbaJs, $Delimiter = '"', $Rasta = False)
    {
        $this->RasbaJS = $RasbaJs;
        $this->Delimiter = $Delimiter;
        $this->Rasta = $Rasta;

        if ($Rasta) {
            $this->FunctionName = 'rasbaJsFunction_' . $this->RasbaJS->randomName();
            $this->CallCode = $this->FunctionName . '();';
            $this->Code .= 'function ' . $this->FunctionName . ' () {';
        }
    }

    public function Document()
    {
        return new Document($this);
    }

    public function Alert($Message = '')
    {
        $this->Code .= 'alert(' . $this->Delimiter . addslashes($Message) . $this->Delimiter . ');';
    }

    public function Run($userFunction)
    {
        if ($this->Rasta) {
            call_user_func($userFunction, $this);
            return $this;
        } else {
            $this->Code = '';
            $this->FunctionName = 'rasbaJsFunction_' . $this->RasbaJS->randomName();
            $this->CallCode = $this->FunctionName . '();';
            $this->Code .= 'function ' . $this->FunctionName . ' () {';

            call_user_func($userFunction, $this);

            $this->Code .= '};';
            return $this->Code;
        }
    }

    public function getResult()
    {
        return $this->Rasta ? ($this->Code . '};') : '';
    }
}

class Document extends JavascriptFunction
{
    public $Code = '';
    private $lastFunction = '';

    public function __construct($MainFunction)
    {
        $this->MainFunction = $MainFunction;
        $this->Code = 'document.';
        $this->lastFunction = '';
    }

    public function getElementById($id)
    {
        $this->lastFunction = 'getElementById';
        $this->Code .= 'getElementById(' . $this->MainFunction->Delimiter . $id . $this->MainFunction->Delimiter . ').';
        return $this;
    }

    public function getElementsByName($name)
    {
        $this->lastFunction = 'getElementsByName';
        $this->Code .= 'getElementsByName(' . $this->MainFunction->Delimiter . $name . $this->MainFunction->Delimiter . ')';
        return $this;
    }

    public function getElementsByTagName($name)
    {
        $this->lastFunction = 'getElementsByTagName';
        $this->Code .= 'getElementsByTagName(' . $this->MainFunction->Delimiter . $name . $this->MainFunction->Delimiter . ')';
        return $this;
    }

    public function getElementsByClassName($name)
    {
        $this->lastFunction = 'getElementsByClassName';
        $this->Code .= 'getElementsByClassName(' . $this->MainFunction->Delimiter . $name . $this->MainFunction->Delimiter . ')';
        return $this;
    }

    public function get(int $i)
    {
        if (in_array($this->lastFunction, ['getElementsByClassName', 'getElementsByTagName', 'getElementsByName'])) {
            $this->lastFunction = 'get';
            $this->Code .= '[' . $i . '].';
            return $this;
        } else {
            return $this;
        }
    }

    public function innerHtml($Html)
    {
        if (in_array($this->lastFunction, ['getElementsByClassName', 'getElementsByTagName', 'getElementsByName', ''])) {
            throw new Exception('Need a element', 4, null, 'use get() or getElement', 'You have to use one of getting element functions. If you are sure you are using it, use `get` function.');
        } else {
            $this->Code .= 'innerHTML = ' . $this->MainFunction->Delimiter . addslashes($Html) . $this->MainFunction->Delimiter . ';';
            $this->MainFunction->Code .= $this->Code;
            return $this->Code;
        }
    }

    public function __call($func, $args)
    {
        if (in_array($this->lastFunction, ['getElementsByClassName', 'getElementsByTagName', 'getElementsByName', ''])) {
            throw new Exception('Need a element', 4, null, 'use get() or getElement', 'You have to use one of getting element functions. If you are sure you are using it, use `get` function.');
        } else {
            $this->Code .= $func . '(';
            foreach ($args as $index => $arg) {
                if (is_string($arg)) {
                    $this->Code .= $this->MainFunction->Delimiter . addslashes($arg) . $this->MainFunction->Delimiter . (($index === (count($args) - 1)) ? '' : ', ');
                } else {
                    $this->Code .= addslashes($arg) . (($index === (count($args) - 1)) ? '' : ', ');
                }
            }
            $this->Code .= ');';

            $this->MainFunction->Code .= $this->Code;
            return $this->Code;
        }
    }
}
