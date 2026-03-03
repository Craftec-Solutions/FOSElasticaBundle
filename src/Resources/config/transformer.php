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
        ->set('fos_elastica.model_to_elastica_transformer', \FOS\ElasticaBundle\Transformer\ModelToElasticaAutoTransformer::class)
            ->abstract()
            ->args([
                [], // options
                service('event_dispatcher'),
            ])
            ->call('setPropertyAccessor', [service('fos_elastica.property_accessor')])

        ->set('fos_elastica.model_to_elastica_identifier_transformer', \FOS\ElasticaBundle\Transformer\ModelToElasticaIdentifierTransformer::class)
            ->abstract()
            ->args([[]]) // options
            ->call('setPropertyAccessor', [service('fos_elastica.property_accessor')])
    ;
};
