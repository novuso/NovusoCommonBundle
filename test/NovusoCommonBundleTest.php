<?php

namespace Novuso\Test\Common\Bundle;

use Novuso\Common\Bundle\DependencyInjection\Compiler\CommandFilterCompilerPass;
use Novuso\Common\Bundle\DependencyInjection\Compiler\CommandHandlerCompilerPass;
use Novuso\Common\Bundle\DependencyInjection\Compiler\EventSubscriberCompilerPass;
use Novuso\Common\Bundle\DependencyInjection\Compiler\HttpResponderCompilerPass;
use Novuso\Common\Bundle\DependencyInjection\Compiler\QueryFilterCompilerPass;
use Novuso\Common\Bundle\DependencyInjection\Compiler\QueryHandlerCompilerPass;
use Novuso\Common\Bundle\NovusoCommonBundle;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @covers Novuso\Common\Bundle\NovusoCommonBundle
 */
class NovusoCommonBundleTest extends PHPUnit_Framework_TestCase
{
    public function test_that_compiler_passes_are_added()
    {
        $container = new ContainerBuilder();
        $bundle = new NovusoCommonBundle();
        $bundle->build($container);

        $config = $container->getCompilerPassConfig();
        $passes = $config->getBeforeOptimizationPasses();

        $commandFilter = false;
        $commandHandler = false;
        $eventSubscriber = false;
        $httpResponder = false;
        $queryFilter = false;
        $queryHandler = false;

        foreach ($passes as $pass) {
            if ($pass instanceof CommandFilterCompilerPass) {
                $commandFilter = true;
            } elseif ($pass instanceof CommandHandlerCompilerPass) {
                $commandHandler = true;
            } elseif ($pass instanceof EventSubscriberCompilerPass) {
                $eventSubscriber = true;
            } elseif ($pass instanceof HttpResponderCompilerPass) {
                $httpResponder = true;
            } elseif ($pass instanceof QueryFilterCompilerPass) {
                $queryFilter = true;
            } elseif ($pass instanceof QueryHandlerCompilerPass) {
                $queryHandler = true;
            }
        }

        $compilerPassesAdded = $commandFilter
            && $commandHandler
            && $eventSubscriber
            && $httpResponder
            && $queryFilter
            && $queryHandler;

        $this->assertTrue($compilerPassesAdded);
    }
}
