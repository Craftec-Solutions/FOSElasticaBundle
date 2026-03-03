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
        ->set('fos_elastica.in_place_pager_persister', \FOS\ElasticaBundle\Persister\InPlacePagerPersister::class)
            ->args([
                service('fos_elastica.persister_registry'),
                service('event_dispatcher'),
            ])
            ->tag('fos_elastica.pager_persister', ['persisterName' => 'in_place'])

        ->set('fos_elastica.pager_persister_registry', \FOS\ElasticaBundle\Persister\PagerPersisterRegistry::class)
            ->args([tagged_locator('fos_elastica.pager_persister', indexAttribute: 'persisterName')])

        ->set('fos_elastica.persister_registry', \FOS\ElasticaBundle\Persister\PersisterRegistry::class)
            ->args([tagged_locator('fos_elastica.persister', indexAttribute: 'index')])

        ->set('fos_elastica.filter_objects_listener', \FOS\ElasticaBundle\Persister\Listener\FilterObjectsListener::class)
            ->args([service('fos_elastica.indexable')])
            ->tag('kernel.event_subscriber')

        ->set('fos_elastica.object_persister', \FOS\ElasticaBundle\Persister\ObjectPersister::class)
            ->abstract()
            ->args([
                '', // index
                '', // model to elastica transformer
                '', // model
                '', // properties mapping
                '', // options
            ])

        ->set('fos_elastica.object_serializer_persister', \FOS\ElasticaBundle\Persister\ObjectSerializerPersister::class)
            ->abstract()
            ->args([
                '', // type
                '', // model to elastica transformer
                '', // model
                '', // serializer
                '', // options
            ])
    ;
};
