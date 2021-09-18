<?php

namespace Rasba;

use Tholu\Packer\Packer;

class JavaScript
{
    /**
     * @var string
     */
    public $RasbaJS = '';
    /**
     * @var array
     */
    public $settings = ['onload' => true, 'minify' => true];

    /**
     * @param array $settings
     */
    public function __construct($settings = [])
    {
        $this->settings = array_merge($this->settings, $settings);
        $this->ids = [];
        $this->JsFunc = new JavascriptFunction($this, '"', true);
    }

    /**
     * @param $code
     * @return mixed
     */
    public function addRasbaJs($code)
    {
        return $this->RasbaJS .= $code;
    }

    /**
     * @param $id
     * @param $icerik
     * @param $attr
     * @return null
     */
    public function addElement($id, $icerik, $attr)
    {
        $this->JsFunc->Run(function ($Js) use ($id, $icerik, $attr) {
            if ($icerik !== '') {
                $Js->Document()->getElementById($id)->innerHtml($icerik);
            }

            array_walk($attr, function ($value, $att) use ($id, $Js) {
                if ($att == 'id') {
                    return;
                }

                $Js->Document()->getElementById($id)->setAttribute($att, $value);
            });
        });
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        $this->RasbaJS .= $this->JsFunc->getResult() . 'window.onload=' . $this->JsFunc->CallCode;
        if ($this->settings['minify']) {
            $minify_settings = ['Normal', false, false, true];
            $minify_settings = array_merge($this->settings['minify_settings'] ?? [], $minify_settings);

            $packer = new Packer($this->RasbaJS, $minify_settings[0], $minify_settings[1], $minify_settings[2], $minify_settings[3]);
            $this->RasbaJS = $packer->pack();
        }

        return $this->RasbaJS;
    }

    /**
     * @param $len
     * @return mixed
     */
    public function randomName($len = null)
    {
        $len == null ? $len = rand(5, 14) : $len = $len;

        while (true) {
            $id = substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz", 4)), 0, $len);
            if (!in_array($id, $this->ids)) {
                $this->ids[] = $id;
                return $id;
            }
        }

        return $id;
    }
}
