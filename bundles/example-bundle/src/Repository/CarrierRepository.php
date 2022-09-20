<?php

namespace Armin\ExampleBundle\Repository;

use Armin\ExampleBundle\Entity\Carrier;
use Armin\ExampleBundle\Event\CarrierFindAllEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @extends ServiceEntityRepository<Carrier>
 *
 * @method Carrier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Carrier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Carrier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarrierRepository extends ServiceEntityRepository
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(ManagerRegistry $registry, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($registry, Carrier::class);
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return Carrier[]
     */
    public function findAll(): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        /** @var CarrierFindAllEvent $event */
        $event = $this->dispatcher->dispatch(new CarrierFindAllEvent($queryBuilder));

        return $event->getQueryBuilder()->getQuery()->getResult();
    }


    public function add(Carrier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Carrier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
