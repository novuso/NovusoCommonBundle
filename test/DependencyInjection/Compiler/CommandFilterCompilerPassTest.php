<?php

namespace Novuso\Test\Common\Bundle\DependencyInjection\Compiler;

use Novuso\Common\Bundle\DependencyInjection\Compiler\CommandFilterCompilerPass;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @covers Novuso\Common\Bundle\DependencyInjection\Compiler\CommandFilterCompilerPass
 */
class CommandFilterCompilerPassTest extends PHPUnit_Framework_TestCase
{
    public function test_that_it_returns_without_error_with_empty_container()
    {
        $container = new ContainerBuilder();
        $compilerPass = new CommandFilterCompilerPass();
        $compilerPass->process($container);
        $this->assertFalse($container->has('novuso_common.command_pipeline'));
    }

    public function test_that_process_adds_tagged_filters_to_pipeline()
    {
        $container = require dirname(dirname(__DIR__)).'/Resources/container.php';
        $compilerPass = new CommandFilterCompilerPass();
        $compilerPass->process($container);
        $definition = $container->findDefinition('novuso_common.command_pipeline');
        $this->assertTrue($definition->hasMethodCall('addFilter'));
    }
}
