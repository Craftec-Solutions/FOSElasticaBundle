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

use FOS\ElasticaBundle\Command\CreateCommand;
use FOS\ElasticaBundle\Command\DeleteCommand;
use FOS\ElasticaBundle\Command\PopulateCommand;
use FOS\ElasticaBundle\Command\ResetCommand;
use FOS\ElasticaBundle\Command\ResetTemplatesCommand;
use FOS\ElasticaBundle\Command\SearchCommand;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('fos_elastica.command.create', CreateCommand::class)
            ->args([
                service('fos_elastica.index_manager'),
                service('fos_elastica.mapping_builder'),
                service('fos_elastica.config_manager'),
                service('fos_elastica.alias_processor'),
            ])
            ->tag('console.command', ['command' => 'fos:elastica:create'])

        ->set('fos_elastica.command.delete', DeleteCommand::class)
            ->args([service('fos_elastica.index_manager')])
            ->tag('console.command', ['command' => 'fos:elastica:delete'])

        ->set('fos_elastica.command.populate', PopulateCommand::class)
            ->args([
                service('event_dispatcher'),
                service('fos_elastica.index_manager'),
                service('fos_elastica.pager_provider_registry'),
                service('fos_elastica.pager_persister_registry'),
                service('fos_elastica.resetter'),
            ])
            ->tag('console.command', ['command' => 'fos:elastica:populate'])

        ->set('fos_elastica.command.reset', ResetCommand::class)
            ->args([
                service('fos_elastica.index_manager'),
                service('fos_elastica.resetter'),
            ])
            ->tag('console.command', ['command' => 'fos:elastica:reset'])

        ->set('fos_elastica.command.templates_reset', ResetTemplatesCommand::class)
            ->args([service('fos_elastica.template_resetter')])
            ->tag('console.command', ['command' => 'fos:elastica:reset-templates'])

        ->set('fos_elastica.command.search', SearchCommand::class)
            ->args([service('fos_elastica.index_manager')])
            ->tag('console.command', ['command' => 'fos:elastica:search'])
    ;
};
