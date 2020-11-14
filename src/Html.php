<?php
namespace Rasba;
use DiDom\Document;
use Tholu\Packer\Packer;

define('JQUERY_SLIM', 'https://code.jquery.com/jquery-3.4.1.slim.min.js');
define('BOOTSTRAP_MIN', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');

class Html {
    public $Document;
    public $Head;
    public $Body;
    public $RasbaJS;
    public $Run;

    public function __construct($rasbajs = true, $attr = []) {
        $this->Document = new Document();
        $this->Html = $this->Document->createElement('html', '', $attr);

        $this->Head = $this->Document->createElement('head');
        $this->Body = $this->Document->createElement('body');
        $this->RasbaJS = $rasbajs === true ? "window.onload = function() {" : false;
        $this->ids = [];
        $this->Run = false;
    }

    public function __call($tag, $in) {
        if ($this->Run) return;

        $main_attr = substr($tag, -strlen('__')) === '__' ? [] : ['id' => $tag . ' ' . $this->randomName()];
        if (count($in) >= 1) {
            if (!empty($in[1]) && is_array($in[1])) {
                if (!empty($in[1])) unset($in[1]['id']);
                $attr = $main_attr + $in[1];
            } else {
                $attr = $main_attr;
            }
            
            if (substr($tag, 0, strlen('__')) === '__' || substr($tag, -strlen('__')) === '__') {
                return $this->Document->createElement(str_replace('__', '', $tag), $in[0], $attr);
            } else if ($this->RasbaJS) {
                if ($in[0] !== '') $this->RasbaJS .= 'document.getElementById("' . $attr['id'] . '").innerHTML = "' . addslashes($in[0]) . '";';
            
                array_walk($attr, function ($value, $att) use($attr) {
                    if ($att == 'id') return;
                    $this->RasbaJS .= 'document.getElementById("' . $attr['id'] . '").setAttribute("' . addslashes($att) . '", "' . addslashes($value) . '");';
                });
                
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

    public function run($echo = true, $minify = true) {  
        if ($minify) {
            $packer = new Packer($this->RasbaJS . '};', 'Normal', true, false, true);
            $packer = $packer->pack();
        } else {
            $packer = $this->RasbaJS . '};';
        }

        if ($this->RasbaJS) {
            $Script = $this->Document->createElement('script', $packer);     
            $this->addBody($Script);    
        }

        $this->Html->appendChild([
            $this->Head, $this->Body
        ]);

        $this->Run = true;
        
        if ($echo) echo '<!doctype html>' . $this->Html->html(); else return '<!doctype html>' . $this->Html->html();
    }
    
    public function addScript($script, $file = false, $attr = []) {
        if ($this->Run) return;
        if ($file && file_exists($file)) {
            $style = $this->__link__('', ['src' => $script] + $attr);
        } else if (filter_var($script, FILTER_VALIDATE_URL)) {
            $style = $this->__link__('', ['src' => $script] + $attr);
        } else if (!$file) {
            $style = $this->__script__($script, NULL, false);
        }

        return $this->Head->appendChild($style);
    }


    public function addBody($element) {
        if ($this->Run) return;
        return $this->Body->appendChild($element);
    }

    public function addHead($element) {
        if ($this->Run) return;
        return $this->Head->appendChild($element);
    }

    public function addStyle($style, $file = false, $attr = []) {
        if ($this->Run) return;

        if ($file && file_exists($file)) {
            $style = $this->__link__('', ['rel' => 'stylesheet', 'href' => $style] + $attr);
        } else if (filter_var($style, FILTER_VALIDATE_URL)) {
            $style = $this->__link__('', ['rel' => 'stylesheet', 'href' => $style] + $attr);
        } else if (!$file) {
            $style = $this->__style__($style);
        }

        return $this->Head->appendChild($style);
    }
}