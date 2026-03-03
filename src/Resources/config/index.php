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
        ->set('fos_elastica.repository_manager', \FOS\ElasticaBundle\Manager\RepositoryManager::class)

        ->set('fos_elastica.alias_processor', \FOS\ElasticaBundle\Index\AliasProcessor::class)

        ->set('fos_elastica.indexable', \FOS\ElasticaBundle\Provider\Indexable::class)
            ->args([[]]) // array of indexable callbacks keyed by type name

        ->set('fos_elastica.index_prototype', \FOS\ElasticaBundle\Elastica\Index::class)
            ->abstract()
            ->args(['']) // index name
        // tagged with fos_elastica.index in the Extension

        ->set('fos_elastica.index_template_prototype', \FOS\ElasticaBundle\Elastica\IndexTemplate::class)
            ->abstract()
            ->args(['']) // index template name
        // tagged with fos_elastica.index_template in the Extension

        ->set('fos_elastica.index_manager', \FOS\ElasticaBundle\Index\IndexManager::class)
            ->args([
                '', // indexes
                service('fos_elastica.index'), // default index
            ])

        ->alias(\FOS\ElasticaBundle\Index\IndexManager::class, 'fos_elastica.index_manager')

        ->set('fos_elastica.index_template_manager', \FOS\ElasticaBundle\Index\IndexTemplateManager::class)
            ->args(['']) // index templates

        ->alias(\FOS\ElasticaBundle\Index\IndexTemplateManager::class, 'fos_elastica.index_template_manager')

        ->set('fos_elastica.resetter', \FOS\ElasticaBundle\Index\Resetter::class)
            ->args([
                service('fos_elastica.config_manager'),
                service('fos_elastica.index_manager'),
                service('fos_elastica.alias_processor'),
                service('fos_elastica.mapping_builder'),
                service('event_dispatcher'),
            ])

        ->alias(\FOS\ElasticaBundle\Index\Resetter::class, 'fos_elastica.resetter')

        ->set('fos_elastica.template_resetter', \FOS\ElasticaBundle\Index\TemplateResetter::class)
            ->args([
                service('fos_elastica.config_manager.index_templates'),
                service('fos_elastica.mapping_builder'),
                service('fos_elastica.index_template_manager'),
            ])

        ->alias(\FOS\ElasticaBundle\Index\TemplateResetter::class, 'fos_elastica.template_resetter')

        // Abstract definition for all finders.
        ->set('fos_elastica.finder', \FOS\ElasticaBundle\Finder\TransformedFinder::class)
            ->abstract()
            ->args([
                '', // searchable
                '', // transformer
            ])
    ;
};
