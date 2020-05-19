<?php
namespace Routing;

use ClassLoader\Loader;
use Exception;
use http\Params;
use Request\Request;

class Router
{
    private $request;

    private $uris = [];

    private $name;

    private $uri;

    private $callable;

    private $redirect;

    public function __construct()
    {
        $this->request = $_GET;
    }

    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    public function action($callable)
    {
        $this->callable = $callable;

        return $this;
    }

    public function uri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    public function redirectTo($uri)
    {
        $this->redirect = $uri;

        return $this;
    }

    public function default($uri)
    {
        $this->uris['default'] = $this->getUrlKey($uri);
    }

    /**
     * @throws Exception if the uri or name of the route is missing
     */
    public function register()
    {
        if(!isset($this->uri, $this->name))
            return new Exception("The url or name are missing for a route: {$this->uri} || {$this->name}");

        $name = str_ireplace("/{0}", "", $this->name);
        $this->uris[$name]['route'] = $this->uri;
        $this->uris[$name]['controller'] = $this->callable;
//        $this->uris[$name]['parameter'] = $this->hasParameters();

        $this->resetRouteInfo();
    }

//    private function getCleanUri()
//    {
//        $a = explode('/', $this->url());
//        $items = [];
//        foreach($a as $x)
//        {
//            if(!is_numeric($x))
//                $items[] = $x;
//            else
//            {
//                $url = substr($this->url(), 0, strrpos($this->url(), '/'));
//                $this->uris[$url]['parameter'] = $x;
//            }
//        }
//
//        return implode('/', $items);
//    }

    private function getUrlKey()
    {
        if(empty(func_get_args()))
            $uri = $this->url();
        else
            $uri = func_get_args()[0];

        foreach($this->uris as $key => $uris) {
            if($key == $uri || $uris['route'] == $uri)
                return $key;
        }

        return null;
    }

    private function resetRouteInfo()
    {
        $this->uri = null;
        $this->callable = null;
        $this->redirect = null;
    }

    /**
     * @param $name
     * @return bool
     */
    private function uriIsValid($name)
    {
        $a = explode('/', $name);
        $items = [];

        if(!empty($a))
        {
            // Check whether we're dealing with an empty uri,
            // if we do we cannot check for parameters as we know we are dealing with the default uri
            foreach($a as $x)
                if(!is_numeric($x))
                    $items[] = $x;
        }

        $name = implode('/', $items);

        return $this->exist($name);
    }

    /**
     * @return string
     */
    public function url()
    {
        return $this->request['q'];
    }

    /**
     *
     * @param $uri string
     * @throws Exception if the file represented by the url is not present
     */
    public function get($uri)
    {
        if($this->uriIsValid($uri)) {
            if(isset($this->currentRouteInfo()['redirect']))
                route($this->currentRouteInfo()['redirect']);

            $this->prepareContent();
            return;
        }

        throw new Exception("$uri is not a registered route or is there any route that looks like it.");
    }

//    public function getRouteInfo($name)
//    {
//        return $this->uris[$name];
//    }

    public function currentRouteInfo()
    {
        return $this->uris[$this->getUrlKey()];
    }

    public function exist($route)
    {
        foreach($this->uris as $key => $uris) {
            if($key == $route || $uris['route'] == $route)
                return true;
        }

        return false;
    }

    public function getCurrentRoute()
    {
        try {
            $this->get($this->url());
        } catch (Exception $e) {
        }
    }

    protected function prepareContent()
    {
        $data = $this->currentRouteInfo();

        $position = stripos($data['controller'], '@');

        $controller = substr($data['controller'], 0, $position);
        $action = substr($data['controller'], $position + 1, strlen($data['controller']));

        $controller = app()->resolve($controller);

        $body = file_get_contents('php://input');
        $body = json_decode($body, true);
        $body = new Request($body);

        if(isset($data['parameter']))
        {
            if(!is_null($body))
                echo json_encode($controller->$action($data['parameter'], $body));
            else
                echo json_encode($controller->$action($data['parameter']));
        }
        else{
            if(!is_null($body))
                echo json_encode($controller->$action($body));
            else
                echo json_encode($controller->$action());
        }
    }

    public function loadRoutes()
    {
        ob_start();
        Loader::loadFile('routes.php');
        ob_get_clean();

        $this->getCurrentRoute();
    }
}
