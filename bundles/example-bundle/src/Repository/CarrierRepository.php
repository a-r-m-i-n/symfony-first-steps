<?php

namespace Armin\ExampleBundle\Repository;

use App\Dto\CarrierFilterDto;
use Armin\ExampleBundle\Entity\Carrier;
use Armin\ExampleBundle\Event\CarrierFindAllEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function findAllPaginated(int $page = 1, int $limit = 5): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('c');

        /** @var CarrierFindAllEvent $event */
        $event = $this->dispatcher->dispatch(new CarrierFindAllEvent($queryBuilder));

        return $this->paginate($event->getQueryBuilder()->getQuery(), $page, $limit);
    }

    public function findByFilterDtoPaginated(CarrierFilterDto $filterDto, int $page, int $limit)
    {
        $queryBuilder = $this->createQueryBuilder('c');
        if ($filterDto->query !== null) {
            $queryBuilder->andWhere('c.name LIKE :val')->setParameter('val', '%' . $filterDto->query . '%');
        }
        if ($filterDto->isCool !== null) {
            $queryBuilder->andWhere('c.isCool = :cool')->setParameter('cool', $filterDto->isCool);
        }

        return $this->paginate($queryBuilder->getQuery(), $page, $limit);

    }

    private function paginate(Query $dql, int $page, int $limit = 2): Paginator
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
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
