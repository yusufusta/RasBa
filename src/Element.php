<?php

namespace Rasba;

use HtmlGenerator\HtmlTag;

class Element
{
    public $tag;
    public $element;
    public $attrs = [];

    public function __construct($tag, $inText = NULL, array $attr = NULL, $RasBa = NULL)
    {
        $this->tag = $tag;
        $this->element = HtmlTag::createElement($tag);

        if ($inText !== NULL) {
            $this->element->text($inText);
        }

        if ($attr !== NULL) {
            $this->attrs = array_merge($this->attrs, $attr);
            $this->element->attr($attr);
        }

        $this->RasBa = $RasBa;
    }

    public function __get($attr)
    {
        if (!empty($this->attrs[$attr])) {
            return $this->attrs[$attr];
        } else {
            return '';
        }
    }

    public function get($attr)
    {
        if (!empty($this->attrs[$attr])) {
            return $this->attrs[$attr];
        } else {
            return '';
        }
    }

    public function set($attr, $value = NULL)
    {
        $this->attrs[$attr] = $value;
        $this->element->set($attr, $value);
        return $this;
    }

    public function setText($text)
    {
        $this->element->text($text);
        return $this;
    }

    public function addChild($element)
    {
        if (is_array($element)) {
            $return = [$this];
            array_map(function ($e) use ($return) {
                if ($e instanceof self) {
                    $this->element->addElement($e->element);
                    $return[] = $e;
                } else if (is_array($e)) {
                    $elmnt = new Element($e[0], empty($e[1]) ? NULL : $e[1], empty($e[2]) ? NULL : $e[2]);
                    $this->element->addElement($elmnt->element);
                    $return[] = $elmnt;
                } else if (is_string($e)) {
                    $elmnt = new Element($e);
                    $this->element->addElement($elmnt->element);
                    $return[] = $elmnt;
                }
            }, $element);
            return $return;
        } else if ($element instanceof self) {
            $this->element->addElement($element->element);
            return [$this, $element];
        } else if (is_string($element)) {
            $elmnt = new Element($element);
            $this->element->addElement($elmnt);
            return [$this, $elmnt];
        } else {
            throw new Exception('Unknown element type', 3, null, '', '');
        }
    }

    public function toBody()
    {
        $this->RasBa->addBody($this);
    }

    public function toHead()
    {
        $this->RasBa->addHead($this);
    }

    public function html()
    {
        return $this->element->__toString();
    }
}
