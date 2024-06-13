<?php

namespace Integration\Repository;

use App\Entity\Rabbit;
use App\Repository\RabbitRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RabbitRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;
    private RabbitRepository $rabbitRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        $this->rabbitRepository = $this->entityManager->getRepository(Rabbit::class);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testAdd()
    {
        $rabbit = new Rabbit();
        $rabbit->setName('Test');

        $this->entityManager->persist($rabbit);
        $this->entityManager->flush();

        $rabbits = $this->rabbitRepository->findAll();

        $this->assertGreaterThan(0, sizeof($rabbits));
    }

}
