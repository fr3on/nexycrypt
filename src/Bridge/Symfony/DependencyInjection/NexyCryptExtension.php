<?php

namespace Nexy\NexyCrypt\Bridge\Symfony\DependencyInjection;

use Nexy\NexyCrypt\NexyCryptFactory;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class NexyCryptExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(
            $this->getConfiguration($configs, $container),
            $configs
        );

        $container->setParameter('nexy_crypt.private_key_path', $config['private_key_path']);
        $container->setParameter('nexy_crypt.endpoint', $config['endpoint']);
        $container->setAlias('nexy_crypt.http.client', new Alias($config['http']['client'], false));

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('sdk.xml');

        if (class_exists(MonologBundle::class)) {
            $container->getDefinition(NexyCryptFactory::class)->addMethodCall('setLogger', [
                new Reference('logger'),
            ]);
        }
    }
}
