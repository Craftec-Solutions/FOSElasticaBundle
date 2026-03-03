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
        ->set('fos_elastica.config_source.container', \FOS\ElasticaBundle\Configuration\Source\ContainerSource::class)
            ->args([[]]) // index configs
            ->tag('fos_elastica.config_source')

        ->set('fos_elastica.config_source.template_container', \FOS\ElasticaBundle\Configuration\Source\TemplateContainerSource::class)
            ->args([[]]) // index configs
            ->tag('fos_elastica.config_source', ['source' => 'index_template'])
    ;
};
