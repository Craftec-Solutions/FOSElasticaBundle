<?php

/*
 * This file is part of the FOSElasticaBundle package.
 *
 * (c) FriendsOfSymfony <https://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\ElasticaBundle\Tests\Unit\Doctrine;

use Doctrine\Persistence\ManagerRegistry;
use Elastica\Result;
use FOS\ElasticaBundle\Doctrine\AbstractElasticaToModelTransformer;
use FOS\ElasticaBundle\Doctrine\ORM\ElasticaToModelTransformer;
use FOS\ElasticaBundle\HybridResult;
use FOS\ElasticaBundle\Transformer\HighlightableModelInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @internal
 */
class AbstractElasticaToModelTransformerTest extends TestCase
{
    /**
     * @var ManagerRegistry|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $registry;

    /**
     * @var string
     */
    protected $objectClass = 'stdClass';

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
    }

    /**
     * Tests if ignore_missing option is properly handled in transformHybrid() method.
     */
    public function testIgnoreMissingOptionDuringTransformHybrid(): void
    {
        $transformer = $this->getMockBuilder(ElasticaToModelTransformer::class)
            ->onlyMethods(['findByIdentifiers'])
            ->setConstructorArgs([$this->registry, $this->objectClass, ['ignore_missing' => true]])
            ->getMock()
        ;

        $transformer->setPropertyAccessor(PropertyAccess::createPropertyAccessor());

        $firstOrmResult = new \stdClass();
        $firstOrmResult->id = 1;
        $secondOrmResult = new \stdClass();
        $secondOrmResult->id = 3;
        $transformer->expects($this->once())
            ->method('findByIdentifiers')
            ->with([1, 2, 3])
            ->willReturn([$firstOrmResult, $secondOrmResult])
        ;

        $firstElasticaResult = new Result(['_id' => 1]);
        $secondElasticaResult = new Result(['_id' => 2]);
        $thirdElasticaResult = new Result(['_id' => 3]);

        $hybridResults = $transformer->hybridTransform([$firstElasticaResult, $secondElasticaResult, $thirdElasticaResult]);

        $this->assertCount(2, $hybridResults);
        $this->assertSame($firstOrmResult, $hybridResults[0]->getTransformed());
        $this->assertSame($firstElasticaResult, $hybridResults[0]->getResult());
        $this->assertSame($secondOrmResult, $hybridResults[1]->getTransformed());
        $this->assertSame($thirdElasticaResult, $hybridResults[1]->getResult());
    }

    public function testObjectClassCanBeSet(): void
    {
        $transformer = $this->createMockTransformer();
        $this->assertSame(Foo::class, $transformer->getObjectClass());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('resultsWithMatchingObjects')]
    public function testObjectsAreTransformedByFindingThemByTheirIdentifiers($elasticaResults, $doctrineObjects): void
    {
        $transformer = $this->createMockTransformer();

        $transformer
            ->expects($this->once())
            ->method('findByIdentifiers')
            ->with($this->equalTo([1, 2, 3]), $this->isType('boolean'))
            ->willReturn($doctrineObjects)
        ;

        $transformedObjects = $transformer->transform($elasticaResults);

        $this->assertSame($doctrineObjects, $transformedObjects);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('resultsWithMatchingObjects')]
    public function testAnExceptionIsThrownWhenTheNumberOfFoundObjectsIsLessThanTheNumberOfResults(
        $elasticaResults,
        $doctrineObjects,
    ): void {
        $transformer = $this->createMockTransformer();

        $transformer
            ->expects($this->once())
            ->method('findByIdentifiers')
            ->with($this->equalTo([1, 2, 3]), $this->isType('boolean'))
            ->willReturn([])
        ;

        $this->expectExceptionMessage(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot find corresponding Doctrine objects (0) for all Elastica results (3). Missing IDs: 1, 2, 3. IDs: 1, 2, 3');

        $transformer->transform($elasticaResults);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('resultsWithMatchingObjects')]
    public function testAnExceptionIsNotThrownWhenTheNumberOfFoundObjectsIsLessThanTheNumberOfResultsIfOptionSet(
        $elasticaResults,
        $doctrineObjects,
    ): void {
        $transformer = $this->createMockTransformer(['ignore_missing' => true]);

        $transformer
            ->expects($this->once())
            ->method('findByIdentifiers')
            ->with($this->equalTo([1, 2, 3]), $this->isType('boolean'))
            ->willReturn([])
        ;

        $results = $transformer->transform($elasticaResults);

        $this->assertSame([], $results);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('resultsWithMatchingObjects')]
    public function testHighlightsAreSetOnTransformedObjects($elasticaResults, $doctrineObjects): void
    {
        $transformer = $this->createMockTransformer();

        $transformer
            ->expects($this->once())
            ->method('findByIdentifiers')
            ->with($this->equalTo([1, 2, 3]), $this->isType('boolean'))
            ->willReturn($doctrineObjects)
        ;

        $results = $transformer->transform($elasticaResults);

        foreach ($results as $result) {
            $this->assertIsArray($result->highlights);
            $this->assertNotEmpty($result->highlights);
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('resultsWithMatchingObjects')]
    public function testResultsAreSortedByIdentifier($elasticaResults, $doctrineObjects): void
    {
        rsort($doctrineObjects);

        $transformer = $this->createMockTransformer();

        $transformer
            ->expects($this->once())
            ->method('findByIdentifiers')
            ->with($this->equalTo([1, 2, 3]), $this->isType('boolean'))
            ->willReturn($doctrineObjects)
        ;

        $results = $transformer->transform($elasticaResults);

        $this->assertSame($doctrineObjects[2], $results[0]);
        $this->assertSame($doctrineObjects[1], $results[1]);
        $this->assertSame($doctrineObjects[0], $results[2]);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('resultsWithMatchingObjects')]
    public function testHybridTransformReturnsDecoratedResults($elasticaResults, $doctrineObjects): void
    {
        $transformer = $this->createMockTransformer();

        $transformer
            ->expects($this->once())
            ->method('findByIdentifiers')
            ->with($this->equalTo([1, 2, 3]), $this->isType('boolean'))
            ->willReturn($doctrineObjects)
        ;

        $results = $transformer->hybridTransform($elasticaResults);

        $this->assertNotEmpty($results);

        foreach ($results as $key => $result) {
            $this->assertInstanceOf(HybridResult::class, $result);
            $this->assertSame($elasticaResults[$key], $result->getResult());
            $this->assertSame($doctrineObjects[$key], $result->getTransformed());
        }
    }

    public static function resultsWithMatchingObjects()
    {
        $elasticaResults = $doctrineObjects = [];
        for ($i = 1; $i < 4; ++$i) {
            $elasticaResults[] = new Result(['_id' => $i, 'highlight' => ['foo']]);
            $doctrineObjects[] = new Foo($i);
        }

        return [
            [$elasticaResults, $doctrineObjects],
        ];
    }

    public function testIdentifierFieldDefaultsToId(): void
    {
        $transformer = $this->createMockTransformer();
        $this->assertSame('id', $transformer->getIdentifierField());
    }

    private function createMockPropertyAccessor()
    {
        $callback = static fn ($object, $identifier) => $object->{$identifier};

        $propertyAccessor = $this->createMock(PropertyAccessorInterface::class);
        $propertyAccessor
            ->expects($this->any())
            ->method('getValue')
            ->with($this->isType('object'), $this->isType('string'))
            ->willReturnCallback($callback)
        ;

        return $propertyAccessor;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|AbstractElasticaToModelTransformer
     */
    private function createMockTransformer($options = [])
    {
        $objectClass = Foo::class;
        $propertyAccessor = $this->createMockPropertyAccessor();

        $transformer = $this->getMockBuilder(AbstractElasticaToModelTransformer::class)
            ->setConstructorArgs([$this->registry, $objectClass, $options])
            ->onlyMethods(['findByIdentifiers'])
            ->getMock();

        $transformer->setPropertyAccessor($propertyAccessor);

        return $transformer;
    }
}

class Foo implements HighlightableModelInterface
{
    public mixed $id;
    public ?array $highlights = null;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function setElasticHighlights(array $highlights): void
    {
        $this->highlights = $highlights;
    }
}
