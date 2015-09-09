<?php

namespace Novuso\Common\Bundle\Http\Resolver;

use LogicException;
use Novuso\Common\Bundle\Http\Responder;

/**
 * ResponderResolver resolves an action class to a responder
 *
 * @copyright Copyright (c) 2015, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
interface ResponderResolver
{
    /**
     * Retrieves a responder for an action
     *
     * @param string $actionClass The full action class name
     *
     * @return Responder
     *
     * @throws LogicException When unable to retrieve a responder
     */
    public function resolve($actionClass);
}
