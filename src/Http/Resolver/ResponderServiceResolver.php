<?php

namespace Novuso\Common\Bundle\Http\Resolver;

/**
 * ResponderServiceResolver resolves responders from a ResponderServiceMap
 *
 * @copyright Copyright (c) 2015, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
class ResponderServiceResolver implements ResponderResolver
{
    /**
     * Responder map
     *
     * @var ResponderServiceMap
     */
    protected $responderMap;

    /**
     * Constructs ResponderServiceResolver
     *
     * @param ResponderServiceMap $responderMap The responder map
     */
    public function __construct(ResponderServiceMap $responderMap)
    {
        $this->responderMap = $responderMap;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($actionClass)
    {
        return $this->responderMap->getResponder($actionClass);
    }
}
