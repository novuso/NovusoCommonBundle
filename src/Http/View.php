<?php

namespace Novuso\Common\Bundle\Http;

use Novuso\System\Type\Type;
use Symfony\Component\HttpFoundation\Request;

/**
 * View represents data from the domain with contextual meta data
 *
 * @copyright Copyright (c) 2015, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
class View
{
    /**
     * Request
     *
     * @var Request
     */
    protected $request;

    /**
     * Action type
     *
     * @var Type
     */
    protected $action;

    /**
     * Domain data
     *
     * @var mixed
     */
    protected $data;

    /**
     * View parameters
     *
     * @var array
     */
    protected $parameters;

    /**
     * Constructs View
     *
     * @param Request $request    The request
     * @param Type    $action     The action type
     * @param mixed   $data       The domain data
     * @param array   $parameters View parameters
     */
    public function __construct(Request $request, Type $action, $data = null, array $parameters = [])
    {
        $this->request = $request;
        $this->action = $action;
        $this->data = $data;
        $this->parameters = $parameters;
    }

    /**
     * Retrieves the request
     *
     * @return Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Retrieves the action type
     *
     * @return Type
     */
    public function action()
    {
        return $this->action;
    }

    /**
     * Retrieves the domain data
     *
     * @return mixed
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Retrieves the view parameters
     *
     * @return array
     */
    public function parameters()
    {
        return $this->parameters;
    }

    /**
     * Retrieves the mime type
     *
     * @return string|null
     */
    public function mimeType()
    {
        return $this->request->attributes->get('_mime_type');
    }

    /**
     * Retrieves the format
     *
     * @return string|null
     */
    public function format()
    {
        return $this->request->attributes->get('_format');
    }

    /**
     * Retrieves the language
     *
     * @return string|null
     */
    public function language()
    {
        return $this->request->attributes->get('_language');
    }
}
