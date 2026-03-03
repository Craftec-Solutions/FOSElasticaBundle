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
        ->set('fos_elastica.pager_provider_registry', \FOS\ElasticaBundle\Provider\PagerProviderRegistry::class)
            ->args([tagged_locator('fos_elastica.pager_provider', indexAttribute: 'index')])
    ;
};
