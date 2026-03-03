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
    $container->extension('FOS\ElasticaBundle\Tests\Functional\TypeObj', [
        'properties' => [
            'field1' => [
                'type' => 'text',
            ],
            'coll' => [
                'type' => 'array<string>',
            ],
        ],
    ]);
};
