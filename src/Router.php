<?php
namespace Rasba;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Regex\Regex;

class Router {
    public $allowMethods = ['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS', 'PATCH', 'ANY'];
    public $requestUri = '';
    public $routers = [];
    public $head = [];
    public $nf = [];
    private $rasbaHtmlSettings = [];

    public function __construct($rasbaHtmlSettings = []) {
        $this->rasbaHtmlSettings = $rasbaHtmlSettings;
        if (empty($rasbaHtmlSettings['html_attr'])) $this->rasbaHtmlSettings['html_attr'] = [];
    }

    public function __call($method, $params) {
        $method = strtoupper($method);
        if (!in_array($method, $this->allowMethods)) throw new Exception('Geçersiz method', 1, null, 'Geçersiz bir method girdiniz', 'Geçersiz bir HTTP methodu girdiniz. Lütfen bunlardan birisini seçiniz: ' . implode(",", $this->allowMethods));
        return $this->routers[] = [$method, $params];
    }

    public function setHead($function) {
        return $this->head = $function;
    }

    public function setNotFound($function) {
        return $this->nf = [$function];
    }

    public function run() {
        $isFound = false;
        foreach ($this->routers as $router) {
            $match = Regex::match('~^' . $router[1][0] . '$~ixs', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
            if ((is_array($router[0]) && count(array_diff($router[0], $this->allowMethods)) !== count($this->allowMethods)) ||
            (in_array($router[0], $this->allowMethods)) && ($router[0] == 'ANY' || (is_array($router[0]) && in_array($_SERVER['REQUEST_METHOD'], $router[0])) || $_SERVER['REQUEST_METHOD'] == $router[0]) && $match->hasMatch()) {
                $request = Request::createFromGlobals();
                $rasba = new Html(new Response(), $this->rasbaHtmlSettings['html_attr'], $this->head, $this->rasbaHtmlSettings);
                $router[1][1]($request, $rasba, $match);
                $rasba->run();
                $isFound = true;
                break;
            }
        }

        if (!$isFound) {
            $response = new Response();
            $response->setStatusCode(404);
            $request = Request::createFromGlobals();
            $rasba = new Html($response, [], $this->head);
            if (count($this->nf) >= 1) {
                $this->nf[0]($request, $rasba);
            } else {
                $h1 = $rasba->h1('404: Not Found!');
                $rasba->addBody($h1);
            }   
            $rasba->run();
        } 
    }
}