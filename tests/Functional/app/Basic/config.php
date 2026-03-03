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
    $container->import(__DIR__.'/./../config/config.php');

    $container->extension('twig', [
        'debug' => '%kernel.debug%',
    ]);

    $container->extension('fos_elastica', [
        'clients' => [
            'default' => [
                'hosts' => [
                    'http://%fos_elastica.host%:%fos_elastica.port%',
                    'http://%fos_elastica.host%:%fos_elastica.port%',
                ],
                'client_config' => [
                    'ssl_verify' => false,
                ],
                'connection_strategy' => 'RoundRobin',
            ],
            'second_server' => [
                'hosts' => [
                    'http://%fos_elastica.host%:%fos_elastica.port%',
                ],
                'connection_strategy' => 'RoundRobin',
            ],
            'third' => [
                'hosts' => [
                    'http://%fos_elastica.host%:%fos_elastica.port%',
                ],
            ],
        ],
        'indexes' => [
            'index' => [
                'index_name' => 'foselastica_basic_test_%kernel.environment%',
                'settings' => [
                    'analysis' => [
                        'analyzer' => [
                            'my_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'lowercase',
                                'filter' => [
                                    'my_ngram',
                                ],
                            ],
                        ],
                        'filter' => [
                            'my_ngram' => [
                                'type' => 'ngram',
                                'min_gram' => 3,
                                'max_gram' => 4,
                            ],
                        ],
                    ],
                ],
                'dynamic' => 'strict',
                'date_detection' => false,
                'dynamic_date_formats' => [
                    'yyyy-MM-dd',
                ],
                'dynamic_templates' => [
                    [
                        'dates' => [
                            'match' => 'date_*',
                            'mapping' => [
                                'type' => 'date',
                            ],
                        ],
                    ],
                    [
                        'strings' => [
                            'match' => '*',
                            'mapping' => [
                                'analyzer' => 'english',
                                'type' => 'text',
                            ],
                        ],
                    ],
                ],
                'numeric_detection' => true,
                'properties' => [
                    'field1' => null,
                    'field2' => [
                        'store' => false,
                    ],
                    'date' => null,
                    'completion' => [
                        'type' => 'completion',
                    ],
                    'title' => [
                        'analyzer' => 'my_analyzer',
                    ],
                    'content' => null,
                    'comments' => [
                        'type' => 'nested',
                        'properties' => [
                            'date' => null,
                            'content' => null,
                        ],
                    ],
                    'multiple' => [
                        'type' => 'text',
                        'fields' => [
                            'name' => [
                                'type' => 'text',
                            ],
                            'position' => [
                                'type' => 'text',
                            ],
                        ],
                    ],
                    'user' => [
                        'type' => 'object',
                    ],
                    'approver' => [
                        'type' => 'object',
                        'properties' => [
                            'date' => null,
                        ],
                    ],
                    'lastlogin' => [
                        'type' => 'date',
                        'format' => 'basic_date_time',
                    ],
                    'birthday' => [
                        'type' => 'date',
                        'format' => 'yyyy-MM-dd',
                    ],
                    'dynamic_allowed' => [
                        'type' => 'object',
                        'dynamic' => true,
                    ],
                ],
            ],
            'null_mappings_index' => [
                'index_name' => 'foselastica_basic_test_%kernel.environment%',
                'properties' => null,
            ],
            'empty_index' => null,
        ],
        'index_templates' => [
            'index_template_example_1' => [
                'client' => 'default',
                'template_name' => 'index_template_1_name',
                'index_patterns' => [
                    'index_template_1_name_*',
                ],
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 0,
                ],
                'properties' => [
                    'document_name_field_1' => [
                        'type' => 'text',
                        'index' => false,
                    ],
                ],
            ],
            'index_template_example_2' => [
                'client' => 'default',
                'template_name' => 'index_template_2_name',
                'index_patterns' => [
                    'index_template_2_name_*',
                ],
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 0,
                ],
                'properties' => [
                    'document_name_field_2' => [
                        'type' => 'text',
                        'index' => false,
                    ],
                ],
            ],
            'index_template_example_3' => [
                'client' => 'default',
                'template_name' => 'index_template_3_name',
                'index_patterns' => [
                    'index_template_3_name_index',
                ],
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 0,
                ],
                'properties' => [
                    'document_name_field_2' => [
                        'type' => 'text',
                        'index' => false,
                    ],
                ],
            ],
        ],
    ]);
};
