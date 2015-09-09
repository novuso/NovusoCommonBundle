<?php

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Novuso\Common\Application\Messaging\Command\CommandPipeline;
use Novuso\Common\Application\Messaging\Command\Filter\CommandLogger;
use Novuso\Common\Application\Messaging\Command\Resolver\CommandServiceMap;
use Novuso\Common\Application\Messaging\Command\Resolver\CommandServiceResolver;
use Novuso\Common\Bundle\DependencyInjection\ContainerAdapter;
use Novuso\Common\Bundle\Logging\LoggerAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

$container = new ContainerBuilder();

$container
    ->register('novuso_common.service_container', ContainerAdapter::class)
    ->addArgument($container);

$container
    ->register('logger', Logger::class)
    ->addArgument('test')
    ->addMethodCall('pushHandler', [new Reference('logger.handler')]);

$container
    ->register('logger.handler', TestHandler::class);

$container
    ->register('novuso_common.logger', LoggerAdapter::class)
    ->addArgument(new Reference('logger'));

$container
    ->register('novuso_common.command_pipeline', CommandPipeline::class)
    ->addArgument(new Reference('novuso_common.command_service_resolver'));

$container
    ->register('novuso_common.command_service_resolver', CommandServiceResolver::class)
    ->addArgument(new Reference('novuso_common.command_service_map'));

$container
    ->register('novuso_common.command_service_map', CommandServiceMap::class)
    ->addArgument(new Reference('novuso_common.service_container'));

$definitionCommandLogger = new Definition(CommandLogger::class, [new Reference('novuso_common.logger')]);
$definitionCommandLogger->addTag('novuso_common.command_filter');
$container->setDefinition('novuso_common.command_logger', $definitionCommandLogger);

return $container;
