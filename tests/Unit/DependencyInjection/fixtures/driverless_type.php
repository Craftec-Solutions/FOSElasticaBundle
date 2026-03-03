<?php

declare(strict_types=1);

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
    $container->extension('fos_elastica', [
        'clients' => [
            'default' => [
                'hosts' => [
                    'http://localhost:9200',
                ],
            ],
        ],
        'indexes' => [
            'test_index' => [
                'client' => 'default',
                'persistence' => [
                    'elastica_to_model_transformer' => [
                        'service' => 'custom.transformer.service',
                    ],
                    'persister' => [
                        'service' => 'custom.persist.service',
                    ],
                ],
                'properties' => [
                    'text' => null,
                ],
            ],
        ],
    ]);
};
