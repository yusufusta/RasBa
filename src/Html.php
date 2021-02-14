<?php

namespace Rasba;

class Html
{
    public $Response;
    public $RasbaJS = true;
    public $Run = false;

    public function __construct($response, $Settings = [], $Match = NULL)
    {
        $this->Response = $response;
        $this->Settings = $Settings;
        $this->Match = $Match;

        $this->Html = new Element('html', '', empty($Settings['attrs']['html']) ? [] : $Settings['attrs']['html'], $this);
        $this->Head = new Element('head', '', empty($Settings['attrs']['head']) ? [] : $Settings['attrs']['head'], $this);
        $this->Body = new Element('body', '', empty($Settings['attrs']['body']) ? [] : $Settings['attrs']['body'], $this);
        $this->ids = [];


        if (array_key_exists('rasbajs', $this->Settings) && $this->Settings['rasbajs'] === false) {
            $this->RasbaJS = false;
        } else {
            $this->RasbaJS = new JavaScript(empty($this->Settings['rasbajs']) ? [] : $this->Settings['rasbajs']);
        }

        if (array_key_exists('database', $this->Settings)) {
            if (!is_array($this->Settings['database'])) {
                throw new Exception('Invalid Database', 2, null, 'DataBase infos are must be array', 'DataBase infos are must be array.');
            } else {
                $this->db = \ParagonIE\EasyDB\Factory::fromArray($this->Settings['database']);
            }
        }

        if (!empty($Settings['head'])) {
            if ($this->Settings['head'] instanceof \Closure) {
                $this->Head->addChild(call_user_func($this->Settings['head'], $this));
            } else {
                $this->Head->addChild($this->Settings['head']);
            }
        }

        if (!empty($Settings['body_top'])) {
            if ($this->Settings['body_top'] instanceof \Closure) {
                $this->Body->addChild(call_user_func($this->Settings['body_top'], $this));
            } else {
                $this->Body->addChild($this->Settings['body_top']);
            }
        }
    }

    public function __call($tag, $in)
    {
        $dontUseRasbaJS = false;
        if (substr($tag, 0, 2) === '__' || $this->RasbaJS === false) {
            if (substr($tag, 0, 2) === '__') $tag = substr($tag, 2);
            $attr = [];
            $dontUseRasbaJS = true;
        } else {
            $attr = ['id' => $this->randomName()];
        }

        if (count($in) >= 1) {
            if (!empty($in[1]) && is_array($in[1])) {
                if (!$dontUseRasbaJS) unset($in[1]['id']);
                $attr = array_merge($attr, $in[1]);
            }

            if ($dontUseRasbaJS) {
                return new Element($tag, $in[0], $attr, $this);
            } else {
                $this->RasbaJS->addElement($attr['id'], $in[0], $attr);
                return new Element($tag, '', ['id' => $attr['id']], $this);
            }
        } else {
            return new Element($tag, '', [], $this);
        }
    }

    public function randomName($len = NULL)
    {
        if (!empty($this->Settings['random_id_len'])) {
            if (is_array($this->Settings['random_id_len'])) {
                $len = rand($this->Settings['random_id_len'][0], $this->Settings['random_id_len'][1]);
            } else if (is_integer($this->Settings['random_id_len'])) {
                $len = $this->Settings['random_id_len'];
            }
        }

        $len == NULL ? $len = rand(5, 14) : $len = $len;

        while (True) {
            $id = substr(str_shuffle(str_repeat(empty($this->Settings['random_id_str']) ? "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz" : $this->Settings['random_id_str'], 4)), 0, $len);
            if (!in_array($id, $this->ids)) {
                $this->ids[] = $id;
                return $id;
            }
        }

        return $id;
    }

    public function addBody($elements)
    {
        if ($this->Run) return;
        $this->Body->addChild($elements);
    }

    public function addHead($elements)
    {
        if ($this->Run) return;
        $this->Head->addChild($elements);
    }

    public function run()
    {
        if ($this->Run) return;
        if (!empty($this->Settings['body_bottom'])) {
            if ($this->Settings['body_bottom'] instanceof \Closure) {
                $this->Body->addChild(call_user_func($this->Settings['body_bottom'], $this));
            } else {
                $this->Body->addChild($this->Settings['body_bottom']);
            }
        }

        if ($this->RasbaJS !== false) {
            $Script = new Element('script', $this->RasbaJS->getResult(), ['type' => 'text/javascript'], $this);
            $Script->toBody();
        }

        $this->Html->addChild([
            $this->Head, $this->Body
        ]);

        $this->Run = true;
        $this->Response->setContent('<!doctype html>' . $this->Html->html());
        $this->Response->send();
    }

    public function addScript($script, $file = false, $appendChild = true, $attr = [])
    {
        if ($this->Run) return;
        if ($file && file_exists($file)) {
            $style = $this->__script__('', ['src' => $script] + $attr);
        } else if (filter_var($script, FILTER_VALIDATE_URL)) {
            $style = $this->__script__('', ['src' => $script] + $attr);
        } else if (!$file) {
            $style = $this->__script__($script, NULL, false);
        }

        return $appendChild ? $this->Head->appendChild($style) : $style;
    }

    public function addStyle($style, $file = false, $appendChild = true, $attr = [])
    {
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

    public function runAndReturnJson($array, $options = 0)
    {
        $this->Response->headers->set('Content-Type', 'application/json');
        $this->Run = true;
        $this->Response->setContent(json_encode($array, $options));
        $this->Response->send();
    }
}
