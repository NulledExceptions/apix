<?php

namespace Zenya\Api;

/**
 * Represents a resource.
 *
 */
class Resource extends Listener
{

    /**
     * Stores the resource methods.
     *
     * @var array
     */
    protected $methods = array();

    /**
     * Import given objects
     *
     * @param array $resources
     */
    public function __construct(Server $server)
    {
        $this->server = $server;

        // attach late listeners @ post-processing
        #$this->addAllListeners('resource', 'early');
    }

    /**
     * Return the classname for a resource (long and private)
     *
     * @param Router $route
     * @return string
     */
    public function setRouteOverrides(Router $route)
    {
        switch ($route->getMethod()) {
            case 'OPTIONS': // resource's help
            case 'HEAD':    // resource's test
                $route->params = array(
                        'resource'  => $route->getController(),
                        'params'    => $route->getParams()
                    );
                $route->setController('HTTP_' . $route->getMethod());
            break;
        }
    }

    /**
     * Call a resource
     *
     * @params string	$name	Name of the resource
     * @return array
     * @throws Zenya\Api\Exception
     */
    public function call()
    {
        $route = $this->server->route;
        $this->setRouteOverrides($route);

        $classArray = $this->server->getResource( $route->getController() );

        $className = isset($classArray['class']) ?  $classArray['class'] : null;
        $classArgs = isset($classArray['classArgs'])
            ? $classArray['classArgs']  // use provided
            : $route->classArgs;        // use route's default

        // map to an action
        $action = $route->getAction();

        try{
            // Relection
            $refClass = new \ReflectionClass($className);
            $this->actions = $refClass->getMethods(\ReflectionMethod::IS_STATIC | \ReflectionMethod::IS_PUBLIC);

            $refMethod = $refClass->getMethod( $route->getAction() );
            // check the actionMethod
            if (
                !in_array($refMethod, $this->actions)
                && !$refMethod->isConstructor()
                && !$refMethod->isAbstract()
            ) {
                throw new Exception();
            }
        } catch(\Exception $e) {
            throw new Exception("Invalid resource's method ({$route->method}) specified.", 405);
        }

        // check the Params
        $params = array();
        foreach ($refMethod->getParameters() as $param) {
            $name = $param->getName();
            if (
                !$param->isOptional()
                && !array_key_exists($name, $route->params)
                && empty($route->params[$name])
            ) {
                throw new Exception("Required {$route->method} parameter \"{$name}\" missing in action.", 400);
            } elseif (isset($route->params[$name])) {
                $params[$name] = $route->params[$name];
            }
        }

        // TODO: maybe we need to check the order of params key match the method?
        // TODO: maybe add a type casting handler here

        // attach late listeners @ post-processing

        // TODO: docs
#        $classDoc = RefDoc::parseDocBook($refClass);
#        $methodDoc = RefDoc::parseDocBook($refMethod);
 
        $this->addAllListeners('resource', 'early');

        return call_user_func_array(array(new $className($classArgs), $action), $params);
    }

    public function getMethods()
    {
        $actions = array();
        if(isset($this->actions)) {
          foreach($this->actions as $obj) {
                  $actions[] = $obj->name;
          }
        }
        $methods = array_intersect($this->server->route->getActions(), $actions);
        return array_keys($methods);
    }

}