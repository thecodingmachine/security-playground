<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function save(Company $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Company $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string|null $search
     * @return array{'id':int, 'name':string, 'userNb':int}
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAggregatedData(?string $search) : array
    {
        // OWASP-3 : SQL Injection
        // Even better use an order by inside Doctrine
        $filters = [];
        if ($search){
            $filters[] = "AND company.name like '%$search%'";
        }
        $sql = "SELECT 
                company.id as id, company.name as name, count(user.id) as userNb
            FROM company 
                LEFT JOIN user ON user.company_id = company.id
            WHERE TRUE %s GROUP BY company.id, company.name";

        return $this->getEntityManager()->getConnection()->executeQuery(sprintf($sql, implode(" AND ", $filters)))->fetchAllAssociative();
    }
}
