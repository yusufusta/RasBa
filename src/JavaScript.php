<?php
namespace Rasba;
use Tholu\Packer\Packer;

class JavaScript {
    public $RasbaJS = '';
    public $Onload = true;

    public function __construct($onload = true) {
        $this->Onload = $onload;
        $this->RasbaJS = 'function rasbajs () {';
    }

    public function addElement($id, $icerik, $attr) {
        if ($icerik !== '') $this->RasbaJS .= 'document.getElementById("' . $id . '").innerHTML = "' . addslashes($icerik) . '";';
            
        array_walk($attr, function ($value, $att) use($attr) {
            if ($att == 'id') return;
            $this->RasbaJS .= 'document.getElementById("' . $attr['id'] . '").setAttribute("' . addslashes($att) . '", "' . addslashes($value) . '");';
        });
    }

    public function getResult($minify = true) {
        $this->RasbaJS .= '};';
        if ($this->Onload) $this->RasbaJS .= 'window.onload=rasbajs();';
        if ($minify) { 
            $packer = new Packer($this->RasbaJS, 'Normal', true, false, true);
            $this->RasbaJS = $packer->pack();
        }

        return $this->RasbaJS;
    }
}