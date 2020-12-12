<?php
namespace Rasba;
use Tholu\Packer\Packer;

class JavaScript {
    public $RasbaJS = '';
    public $settings = ['onload' => true, 'function' => true, 'function_name' => 'rasbajs', 'minify' => true, 
        'custom' => false # 3.0
    ];

    public function __construct($settings = []) {
        $this->settings = array_merge($this->settings, $settings);
        $this->RasbaJS = $this->settings['function'] ? 'function ' . $this->settings['function_name'] . ' () {' : '';
    }

    public function addRasbaJs ($code) {
        return $this->RasbaJS .= $code;
    }

    public function addElement($id, $icerik, $attr) {
        if ($icerik !== '') $this->RasbaJS .= 'document.getElementById("' . $id . '").innerHTML = "' . addslashes($icerik) . '";';
            
        array_walk($attr, function ($value, $att) use($attr) {
            if ($att == 'id') return;
            $this->RasbaJS .= 'document.getElementById("' . $attr['id'] . '").setAttribute("' . addslashes($att) . '", "' . addslashes($value) . '");';
        });
    }

    public function getResult() {
        if ($this->settings['function']) $this->RasbaJS .= '};';
        if ($this->settings['onload']) $this->RasbaJS .= 'window.onload=' . $this->settings['function_name'] . '();';
        
        if ($this->settings['minify']) { 
            $packer = new Packer($this->RasbaJS, 'Normal', true, false, true);
            $this->RasbaJS = $packer->pack();
        }

        return $this->RasbaJS;
    }
}