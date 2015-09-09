<?php

namespace Novuso\Common\Bundle\Http;

use Exception;
use Novuso\System\Type\Type;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Action is the base class for an HTTP request handler
 *
 * @copyright Copyright (c) 2015, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
abstract class Action
{
    /**
     * Request
     *
     * @var Request
     */
    protected $request;

    /**
     * Handles the request
     *
     * @return View
     *
     * @throws Exception When unable to handle the request
     */
    abstract protected function call();

    /**
     * Invokes the action
     *
     * @param Request $request The request
     *
     * @return View
     *
     * @throws Exception When unable to handle the request
     */
    public function __invoke(Request $request)
    {
        $this->request = $request;

        return $this->call();
    }

    /**
     * Retrieves values from the post data
     *
     * @param string|null $key     The key or null for all values
     * @param mixed       $default A default value to return if not found
     * @param bool        $deep    Whether or not to perform a deep search
     *
     * @return mixed
     */
    protected function post($key = null, $default = null, $deep = false)
    {
        return $this->fetchFromRequest('request', $key, $default, $deep);
    }

    /**
     * Retrieves values from the query string
     *
     * @param string|null $key     The key or null for all values
     * @param mixed       $default A default value to return if not found
     * @param bool        $deep    Whether or not to perform a deep search
     *
     * @return mixed
     */
    protected function query($key = null, $default = null, $deep = false)
    {
        return $this->fetchFromRequest('query', $key, $default, $deep);
    }

    /**
     * Retrieves values from the request attributes
     *
     * @param string|null $key     The key or null for all values
     * @param mixed       $default A default value to return if not found
     * @param bool        $deep    Whether or not to perform a deep search
     *
     * @return mixed
     */
    protected function attributes($key = null, $default = null, $deep = false)
    {
        return $this->fetchFromRequest('attributes', $key, $default, $deep);
    }

    /**
     * Retrieves values from the server data
     *
     * @param string|null $key     The key or null for all values
     * @param mixed       $default A default value to return if not found
     * @param bool        $deep    Whether or not to perform a deep search
     *
     * @return mixed
     */
    protected function server($key = null, $default = null, $deep = false)
    {
        return $this->fetchFromRequest('server', $key, $default, $deep);
    }

    /**
     * Retrieves values from the request headers
     *
     * @param string|null $key     The key or null for all values
     * @param mixed       $default A default value to return if not found
     * @param bool        $first   Whether or not to limit to the first match
     *
     * @return mixed
     */
    protected function headers($key = null, $default = null, $first = true)
    {
        return $this->fetchFromRequest('headers', $key, $default, $first);
    }

    /**
     * Retrieves values from the request files
     *
     * @param string|null $key     The key or null for all values
     * @param mixed       $default A default value to return if not found
     * @param bool        $deep    Whether or not to perform a deep search
     *
     * @return mixed
     */
    protected function files($key = null, $default = null, $deep = false)
    {
        return $this->fetchFromRequest('files', $key, $default, $deep);
    }

    /**
     * Retrieves values from the request cookies
     *
     * @param string|null $key     The key or null for all values
     * @param mixed       $default A default value to return if not found
     * @param bool        $deep    Whether or not to perform a deep search
     *
     * @return mixed
     */
    protected function cookies($key = null, $default = null, $deep = false)
    {
        return $this->fetchFromRequest('cookies', $key, $default, $deep);
    }

    /**
     * Retrieves values from the session
     *
     * @param string|null $key     The key or null for all values
     * @param mixed       $default A default value to return if not found
     *
     * @return mixed
     */
    protected function session($key = null, $default = null)
    {
        $session = $this->request->getSession();

        if ($session === null) {
            return ($key === null) ? [] : $default;
        }

        if ($key === null) {
            return $session->all();
        }

        return $session->get($key, $default);
    }

    /**
     * Creates a view for the current request
     *
     * @param mixed $data       The domain data
     * @param array $parameters Additional parameters
     *
     * @return View
     */
    protected function view($data = null, array $parameters = [])
    {
        $action = Type::create($this);

        return new View($this->request, $action, $data, $parameters);
    }

    /**
     * Retrieves values from the request
     *
     * @param string      $source  The source property
     * @param string|null $key     The key or null for all values
     * @param mixed       $default A default value to return if not found
     * @param mixed       $extra   A third argument passed to the get method
     *
     * @return mixed
     */
    private function fetchFromRequest($source, $key, $default, $extra)
    {
        if ($key === null) {
            return $this->request->$source->all();
        }

        return $this->request->$source->get($key, $default, $extra);
    }
}
