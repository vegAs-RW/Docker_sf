<?php

namespace Unit\Entity;

use App\Entity\Rabbit;
use PHPUnit\Framework\TestCase;

class RabbitTest extends TestCase
{
    public function testRabbit()
    {
        $rabbit = new Rabbit();

        $this->assertInstanceOf(Rabbit::class, $rabbit);

        $this->assertNull($rabbit->getId());

        $rabbit->setName('Test');
        $this->assertEquals('Test', $rabbit->getName());
    }
}
