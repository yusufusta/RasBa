<?php

namespace Rasba;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Html
{
    /**
     * @var mixed
     */
    public $Response;
    /**
     * @var mixed
     */
    public $RasbaJS = true;
    /**
     * @var mixed
     */
    public $Run = false;

    /**
     * @var mixed
     */
    public $Request = null;

    /**
     * @param $response
     * @param array $Settings
     * @param $Match
     */
    public function __construct($Response, $Settings = [], $Request = null, $Vars = [])
    {
        $this->Response = $Response;
        $this->Settings = $Settings;
        $this->Request = $Request;
        $this->Vars = $Vars;

        $this->Html = new Element('html', '', $Settings['attrs']['html'] ?? [], $this);
        $this->Head = new Element('head', '', $Settings['attrs']['head'] ?? [], $this);
        $this->Body = new Element('body', '', $Settings['attrs']['body'] ?? [], $this);
        $this->ids = [];

        if (array_key_exists('rasbajs', $this->Settings) && $this->Settings['rasbajs'] === false) {
            $this->RasbaJS = false;
        } else {
            $this->RasbaJS = new JavaScript($this->Settings['rasbajs'] ?? []);
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

        $this->latte = new \Latte\Engine;
        if (array_key_exists('latte', $this->Settings)) {
            $this->latte->setTempDirectory($this->Settings['latte']['temp'] ?? __DIR__ . '/_views_temp/');
        } else {
            $this->latte->setTempDirectory(__DIR__ . '/_views_temp/');
        }
    }

    /**
     * @param $tag
     * @param $in
     */
    public function __call($tag, $in)
    {
        $dontUseRasbaJS = false;
        if (substr($tag, 0, 2) === '__' || $this->RasbaJS === false) {
            if (substr($tag, 0, 2) === '__') {
                $tag = substr($tag, 2);
            }

            $attr = [];
            $dontUseRasbaJS = true;
        } else {
            $attr = ['id' => $this->randomName()];
        }

        if (count($in) >= 1) {
            if (!empty($in[1]) && is_array($in[1])) {
                if (!$dontUseRasbaJS) {
                    unset($in[1]['id']);
                }

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

    /**
     * @param $len
     * @return mixed
     */
    public function randomName($len = null)
    {
        if (!empty($this->Settings['random_id_len'])) {
            if (is_array($this->Settings['random_id_len'])) {
                $len = rand($this->Settings['random_id_len'][0], $this->Settings['random_id_len'][1]);
            } else if (is_integer($this->Settings['random_id_len'])) {
                $len = $this->Settings['random_id_len'];
            }
        }

        $len == null ? $len = rand(5, 14) : $len = $len;

        while (true) {
            $id = substr(str_shuffle(str_repeat(empty($this->Settings['random_id_str']) ? "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz" : $this->Settings['random_id_str'], 4)), 0, $len);
            if (!in_array($id, $this->ids)) {
                $this->ids[] = $id;
                return $id;
            }
        }

        return $id;
    }

    /**
     * @param $elements
     * @return null
     */
    public function addBody($elements)
    {
        if ($this->Run) {
            return;
        }

        $this->Body->addChild($elements);
    }

    /**
     * @param $elements
     * @return null
     */
    public function addHead($elements)
    {
        if ($this->Run) {
            return;
        }

        $this->Head->addChild($elements);
    }

    /**
     * @return null
     */
    public function run()
    {
        if ($this->Run) {
            return;
        }

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
            $this->Head, $this->Body,
        ]);

        $this->Run = true;
        $this->Response->setContent('<!doctype html>' . $this->Html->html());
        $this->Response->send();
    }

    /**
     * @param $script
     * @param $file
     * @param false $appendChild
     * @param array $attr
     * @return mixed
     */
    public function addScript($script, $file = false, $appendChild = true, $attr = [])
    {
        if ($this->Run) {
            return;
        }

        if ($file && file_exists($file)) {
            $style = $this->__script__('', ['src' => $script] + $attr);
        } else if (filter_var($script, FILTER_VALIDATE_URL)) {
            $style = $this->__script__('', ['src' => $script] + $attr);
        } else if (!$file) {
            $style = $this->__script__($script, null, false);
        }

        return $appendChild ? $this->Head->appendChild($style) : $style;
    }

    /**
     * @param $style
     * @param $file
     * @param false $appendChild
     * @param array $attr
     * @return mixed
     */
    public function addStyle($style, $file = false, $appendChild = true, $attr = [])
    {
        if ($this->Run) {
            return;
        }

        if ($file && file_exists($file)) {
            $style = $this->__link__('', ['rel' => 'stylesheet', 'href' => $style] + $attr);
        } else if (filter_var($style, FILTER_VALIDATE_URL)) {
            $style = $this->__link__('', ['rel' => 'stylesheet', 'href' => $style] + $attr);
        } else if (!$file) {
            $style = $this->__style__($style);
        }

        return $appendChild ? $this->Head->appendChild($style) : $style;
    }

    /**
     * @param $array
     * @param $options
     */
    public function returnJson($array, $options = 0)
    {
        $this->Run = true;

        $this->Response->headers->set('Content-Type', 'application/json');
        $this->Response->setContent(json_encode($array, $options));
        $this->Response->send();
    }

    /**
     * @param $view
     * @param array $data
     */
    public function returnView($view, $data = [])
    {
        $this->Run = true;

        $this->Response->headers->set('Content-Type', 'text/html');
        $this->Response->setContent($this->latte->renderToString($view, $data));
        $this->Response->send();
    }

    /**
     * @param $path
     * @param $mime
     */
    public function returnFile($path, $mime = null)
    {
        $this->Run = true;

        $this->Response->headers->set('Content-Type', $mime == null ? mime_content_type($path) : $mime);
        $this->Response->setContent(file_get_contents($path));
        $this->Response->send();
    }

    /**
     * @param $code
     */
    public function returnHttpError($code)
    {
        $this->Run = true;

        $response = new Response();
        $response->setStatusCode($code);
        $request = Request::createFromGlobals();
        $rasba = new Html($response, $this->Settings, $request);

        if (!empty($this->Settings['errors'][$code])) {
            call_user_func($this->Settings['errors'][$code], $rasba);
        } else {
            $rasba->h1($code)->toBody();
        }

        $rasba->run();
    }

    /**
     * @param $url
     * @param $status
     */
    public function returnRedirect($url, $status = 302)
    {
        $this->Run = true;

        $this->Response->headers->set('Location', $url);
        $this->Response->setStatusCode($status);
        $this->Response->send();
    }
}
