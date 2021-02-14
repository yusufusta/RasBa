<?php

namespace Rasba;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Regex\Regex;

class Router
{
    public $allowMethods = ['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS', 'PATCH', 'ANY'];
    public $requestUri = '';
    public $routers = [];
    private $rasbaHtmlSettings = [];

    /**
     * 
     */
    public function __construct($rasbaHtmlSettings = [])
    {
        $this->rasbaHtmlSettings = $rasbaHtmlSettings;
    }

    public function __call($method, $params)
    {
        $method = strtoupper($method);
        if (!in_array($method, $this->allowMethods)) throw new Exception('Invalid method', 1, null, '', 'Please select an method: ' . implode(",", $this->allowMethods));
        return $this->routers[] = [$method, $params];
    }

    public function run()
    {
        $isFound = false;
        foreach ($this->routers as $router) {
            $match = Regex::match('~^' . (empty($this->rasbaHtmlSettings['basepath']) ? '' : $this->rasbaHtmlSettings['basepath']) . $router[1][0] . '$~ixs', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
            if ((is_array($router[0]) && count(array_diff($router[0], $this->allowMethods)) !== count($this->allowMethods)) ||
                (in_array($router[0], $this->allowMethods)) && ($router[0] == 'ANY' || (is_array($router[0]) && in_array($_SERVER['REQUEST_METHOD'], $router[0])) || $_SERVER['REQUEST_METHOD'] == $router[0]) && $match->hasMatch()
            ) {
                $request = Request::createFromGlobals();
                $rasba = new Html(new Response(), $this->rasbaHtmlSettings, $match);
                call_user_func($router[1][1], $request, $rasba);
                $rasba->run();
                $isFound = true;
                break;
            }
        }

        if (!$isFound) {
            $response = new Response();
            $response->setStatusCode(404);
            $request = Request::createFromGlobals();
            $rasba = new Html($response, $this->rasbaHtmlSettings);

            if (!empty($this->rasbaHtmlSettings['errors'][404])) {
                call_user_func($this->rasbaHtmlSettings['errors'][404], $request, $rasba);
            } else {
                $rasba->h1('404: Not Found!')->toBody();
            }

            $rasba->run();
        }
    }
}
