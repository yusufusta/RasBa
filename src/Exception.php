<?php

namespace Rasba;

class Exception extends \Exception
{
    private $Message = '';
    private $Description = '';

    public function __construct($Message, $Code = 0, \Exception $Previous = null, $Error = '', $Description = '')
    {
        $this->Code = $Code;
        $this->Message = $Error;
        $this->Description = $Description;
        parent::__construct($Message, $Code, $Previous);
    }

    public function getErrorCode(): string
    {
        return $this->Code;
    }

    public function getErrorDescription(): string
    {
        return $this->Description;
    }

    public function __toString()
    {
        return "RasBa error!\nError: " . $this->Description . PHP_EOL . 'Backtrace:' . PHP_EOL . $this->getTraceAsString();
    }
}
