<?php
namespace Rasba;
use DiDom\Document;
use Tholu\Packer\Packer;

class Html {
    public $Document;
    public $Response;
    public $Head;
    public $Body;
    public $RasbaJS = true;
    public $Run;
    public $settings = [];

    public function __construct($response, $attr = [], $head, $settings = []) {
        $this->Response = $response;
        $this->Document = new Document();
        $this->Html = $this->Document->createElement('html', '', $attr);

        $this->Head = $this->Document->createElement('head');
        if (!empty($head)) {
            $this->Head->appendChild($head($this->Document));
        }

        $this->Body = $this->Document->createElement('body');
        $this->ids = [];

        if ($this->RasbaJS) {
            $this->RasbaJS = new JavaScript();
        }

        foreach ($settings as $setting => $value) {
            $this->settings[$setting] = $value;
        }

        if (!array_key_exists('minify', $this->settings)) {
            $this->settings['minify'] = true;
        }
    }

    public function __call($tag, $in) {
        $main_attr = substr($tag, -strlen('__')) === '__' ? [] : ['id' => $this->randomName()];
        if (count($in) >= 1) {
            if (!empty($in[1]) && is_array($in[1])) {
                if (!empty($in[1])) unset($in[1]['id']);
                $attr = $main_attr + $in[1];
            } else {
                $attr = $main_attr;
            }
            
            if (substr($tag, 0, strlen('__')) === '__' || substr($tag, -strlen('__')) === '__') {
                return $this->Document->createElement(str_replace('__', '', $tag), $in[0], $attr);
            } else if ($this->RasbaJS !== false) {
                $this->RasbaJS->addElement($attr['id'], $in[0], $attr);
                return $this->Document->createElement($tag, '', $main_attr);
            } else {
                return $this->Document->createElement($tag, $in[0], $attr);
            }
        } else {
            return $this->Document->createElement($tag, '', $main_attr);
        }
    }

    public function add($tag, ...$in) {
        $main_attr = substr($tag, -strlen('__')) === '__' ? [] : ['id' => $this->randomName()];
        if (count($in) >= 1) {
            if (!empty($in[1]) && is_array($in[1])) {
                if (!empty($in[1])) unset($in[1]['id']);
                $attr = $main_attr + $in[1];
            } else {
                $attr = $main_attr;
            }
            
            if (substr($tag, 0, strlen('__')) === '__' || substr($tag, -strlen('__')) === '__') {
                return $this->Document->createElement(str_replace('__', '', $tag), $in[0], $attr);
            } else if ($this->RasbaJS !== false) {
                $this->RasbaJS->addElement($attr['id'], $in[0], $attr);
                return $this->Document->createElement($tag, '', $main_attr);
            } else {
                return $this->Document->createElement($tag, $in[0], $attr);
            }
        } else {
            return $this->Document->createElement($tag, '', $main_attr);
        }
    }

    public function randomName($len = NULL){
        $len == NULL ? $len = rand(5, 14) : $len = $len;
        
        while (True) {
            $id = substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz", $len)), 0, $len);
            if (!in_array($id, $this->ids)) {
                $this->ids[] = $id;
                return $id;
            }
        }

        return $id;
    }

    public function run() { 
        if ($this->RasbaJS !== false) {
            $Script = $this->Document->createElement('script', $this->RasbaJS->getResult($this->settings['minify']), ['type' => 'text/javascript']);
            $this->addBody($Script);    
        }

        $this->Html->appendChild([
            $this->Head, $this->Body
        ]);
        
        $this->Run = true;
        $this->Response->setContent('<!doctype html>' . $this->Html->html());
        $this->Response->send();
    }
    
    public function addBody($element) {
        if ($this->Run) return;
        return $this->Body->appendChild($element);
    }

    public function changeTitle($title, $attr = []) {
        if ($this->Run) return;
        if ($this->Head->has('title')) {
            $this->Head->firstInDocument('title')->remove();
        }
        $element = $this->__title__($title, $attr);
        $this->addHead($element);
        return $element;
    }

    public function addHead($element) {
        if ($this->Run) return;
        return $this->Head->appendChild($element);
    }

    public function addScript($script, $file = false, $appendChild = true, $attr = []) {
        if ($this->Run) return;
        if ($file && file_exists($file)) {
            $style = $this->__link__('', ['src' => $script] + $attr);
        } else if (filter_var($script, FILTER_VALIDATE_URL)) {
            $style = $this->__link__('', ['src' => $script] + $attr);
        } else if (!$file) {
            $style = $this->__script__($script, NULL, false);
        }

        return $appendChild ? $this->Head->appendChild($style) : $style;
    }

    public function addStyle($style, $file = false, $appendChild = true, $attr = []) {
        if ($this->Run) return;

        if ($file && file_exists($file)) {
            $style = $this->__link__('', ['rel' => 'stylesheet', 'href' => $style] + $attr);
        } else if (filter_var($style, FILTER_VALIDATE_URL)) {
            $style = $this->__link__('', ['rel' => 'stylesheet', 'href' => $style] + $attr);
        } else if (!$file) {
            $style = $this->__style__($style);
        }

        return $appendChild ? $this->Head->appendChild($style) : $style;
    }
}