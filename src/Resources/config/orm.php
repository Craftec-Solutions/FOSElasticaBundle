<?php

/*
 * This file is part of the FOSElasticaBundle package.
 *
 * (c) FriendsOfSymfony <https://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('fos_elastica.pager_provider.prototype.orm', \FOS\ElasticaBundle\Doctrine\ORMPagerProvider::class)
            ->abstract()
            ->args([
                service('doctrine'), // manager registry
                service('fos_elastica.doctrine.register_listeners'),
                '', // model
                [], // options
            ])

        ->set('fos_elastica.doctrine.register_listeners', \FOS\ElasticaBundle\Doctrine\RegisterListenersService::class)
            ->args([service('event_dispatcher')])

        ->set('fos_elastica.listener.prototype.orm', \FOS\ElasticaBundle\Doctrine\Listener::class)
            ->abstract()
            ->args([
                '', // object persister
                service('fos_elastica.indexable'),
                [], // configuration
                null, // logger
            ])

        ->set('fos_elastica.elastica_to_model_transformer.prototype.orm', \FOS\ElasticaBundle\Doctrine\ORM\ElasticaToModelTransformer::class)
            ->abstract()
            ->args([
                service('doctrine'),
                '', // model
                [], // options
            ])
            ->call('setPropertyAccessor', [service('fos_elastica.property_accessor')])

        ->set('fos_elastica.manager.orm', \FOS\ElasticaBundle\Doctrine\RepositoryManager::class)
            ->args([
                service('doctrine'),
                service('fos_elastica.repository_manager'),
            ])
    ;
};
