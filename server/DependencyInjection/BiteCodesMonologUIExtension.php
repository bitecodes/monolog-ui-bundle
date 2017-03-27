<?php

namespace BiteCodes\MonologUIBundle\DependencyInjection;

use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class BiteCodesMonologUIExtension extends Extension
{
    protected $defaultChannels = [
        'cache',
        'doctrine',
        'event',
        'php',
        'profiler',
        'request',
        'router',
        'security',
        'templating',
        'translation',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $channels = array_merge($this->defaultChannels, $container->getParameter('monolog.additional_channels'));
        sort($channels);
        
        $container->setParameter('bitecodes_monolog_ui.doctrine.table_name', $config['table']);
        $container->setParameter('bitecodes_monolog_ui.logger.handles', $this->getHandleConfig($config['logger']['handles']));
        $container->setParameter('bitecodes_monolog_ui.channels', $channels);
        $container->setAlias('bitecodes_monolog_ui.doctrine_dbal.connection',
            sprintf('doctrine.dbal.%s_connection', $config['connection']));

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param $handles
     *
     * @return mixed
     */
    protected function getHandleConfig($handles)
    {
        $normalized = [];

        array_walk($handles, function ($value, $key) use (&$normalized) {
            $key = Logger::toMonologLevel($key);
            $normalized[$key] = $value;
        });

        ksort($normalized);

        return array_reverse($normalized, true);
    }
}
