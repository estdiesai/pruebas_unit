<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Dinosaur;
use PHPUnit\Framework\TestCase;

class DinosaurTest extends TestCase
{
    // en las aserciones va primero valor_esperado, valor_real
    public function testItWorks(): void
    {
        self::assertEquals('42', 42);// no falla porque usa ==
    }

    public function testItWorksTheSame(): void
    {
        self::assertSame(42, 42);//falla si lo pongo con '' utiliza ===
    }

    public function testCanGetAndSetData(): void
    {
        $dino = new Dinosaur(
            name: 'Big Eaty',
            genus: 'Tyrannosaurus',
            length: 15,
            enclosure: 'Paddock A',
        );

        self::assertSame('Big Eaty', $dino->getName());
        self::assertSame('Tyrannosaurus', $dino->getGenus());
        self::assertSame(15, $dino->getLength());
        self::assertGreaterThan(10, $dino->getLength());// falla si valor real>esperado
        self::assertSame('Paddock A', $dino->getEnclosure());
    }
    /**
     * @dataProvider sizeDescriptionProvider
     */
    public function testDinoHasCorrectSizeDescriptionFromLength(int $length, string $expectedSize): void
    {
        $dino = new Dinosaur(name: 'Big Eaty', length: $length);

        self::assertSame($expectedSize, $dino->getSizeDescription());
    }

    public function sizeDescriptionProvider(): \Generator
    {
        yield '10 Meter Large Dino' => [10, 'Large'];
        yield '5 Meter Medium Dino' => [5, 'Medium'];
        yield '4 Meter Small Dino' => [4, 'Small'];
    }


    public function testDino10MetersOrGreaterIsLarge(): void
    {
        self::assertGreaterThan(10, 42);
    }
}