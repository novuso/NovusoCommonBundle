<?php

namespace Novuso\Common\Bundle;

use Novuso\Common\Bundle\DependencyInjection\Compiler\CommandFilterCompilerPass;
use Novuso\Common\Bundle\DependencyInjection\Compiler\CommandHandlerCompilerPass;
use Novuso\Common\Bundle\DependencyInjection\Compiler\EventSubscriberCompilerPass;
use Novuso\Common\Bundle\DependencyInjection\Compiler\HttpResponderCompilerPass;
use Novuso\Common\Bundle\DependencyInjection\Compiler\QueryFilterCompilerPass;
use Novuso\Common\Bundle\DependencyInjection\Compiler\QueryHandlerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * NovusoCommonBundle is the Novuso Common Symfony Bundle
 *
 * @copyright Copyright (c) 2015, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
class NovusoCommonBundle extends Bundle
{
    /**
     * Builds in container modifications when cache is empty
     *
     * @param ContainerBuilder $container The container builder
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CommandFilterCompilerPass());
        $container->addCompilerPass(new CommandHandlerCompilerPass());
        $container->addCompilerPass(new EventSubscriberCompilerPass());
        $container->addCompilerPass(new HttpResponderCompilerPass());
        $container->addCompilerPass(new QueryFilterCompilerPass());
        $container->addCompilerPass(new QueryHandlerCompilerPass());
    }
}
