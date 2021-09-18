<?php

namespace Rasba;

use FastRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    /**
     * @var array
     */
    public $allowMethods = ['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS', 'PATCH', 'ANY'];
    /**
     * @var string
     */
    public $requestUri = '';
    /**
     * @var array
     */
    public $routers = [];
    /**
     * @var array
     */
    private $rasbaHtmlSettings = [];

    /**
     *
     */
    public function __construct($rasbaHtmlSettings = [])
    {
        $this->rasbaHtmlSettings = $rasbaHtmlSettings;
    }

    /**
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        $method = strtoupper($method);
        if (!in_array($method, $this->allowMethods)) {
            throw new Exception('Invalid method', 1, null, '', 'Please select an method: ' . implode(",", $this->allowMethods));
        }

        return $this->routers[] = [$method, $params];
    }

    /**
     * @param $method
     * @param $url
     * @param $callback
     * @return mixed
     */
    public function route($method, $url, $callback)
    {
        $method = strtoupper($method);
        if (is_array($method)) {
            foreach ($method as $m) {
                if (!in_array($m, $this->allowMethods)) {
                    throw new Exception('Invalid method: ' . $m, 1, null, '', 'Please select an method: ' . implode(",", $this->allowMethods));
                }

                if (is_array($url)) {
                    foreach ($url as $u) {
                        $this->routers[] = [$m, [$u, $callback]];
                    }
                } else {
                    $this->routers[] = [$m, [$url, $callback]];
                }
            }
        } else {
            if (!in_array($method, $this->allowMethods)) {
                throw new Exception('Invalid method', 1, null, '', 'Please select an method: ' . implode(",", $this->allowMethods));
            }

            if (is_array($url)) {
                foreach ($url as $u) {
                    $this->routers[] = [$method, [$u, $callback]];
                }
            } else {
                $this->routers[] = [$method, [$url, $callback]];
            }
        }
    }

    public function run()
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            foreach ($this->routers as $router) {
                $r->addRoute($router[0], $router[1][0], $router[1][1]);
            }
        });

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        if ($routeInfo[0] == FastRoute\Dispatcher::NOT_FOUND) {
            $response = new Response();
            $response->setStatusCode(404);
            $request = Request::createFromGlobals();
            $rasba = new Html($response, $this->rasbaHtmlSettings, $request);

            if (!empty($this->rasbaHtmlSettings['errors'][404])) {
                call_user_func($this->rasbaHtmlSettings['errors'][404], $rasba);
            } else {
                $rasba->h1('404: Not Found!')->toBody();
            }

            $rasba->run();
        } else if (FastRoute\Dispatcher::FOUND) {
            $response = new Response();
            $request = Request::createFromGlobals();
            $rasba = new Html($response, $this->rasbaHtmlSettings, $request, $routeInfo[2] ?? []);
            call_user_func($routeInfo[1], $rasba);
            $rasba->run();
        }
    }
}
