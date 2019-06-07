<?php

namespace App\Repository;

use App\Entity\Shopcard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Shopcard|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shopcard|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shopcard[]    findAll()
 * @method Shopcard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopcardRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Shopcard::class);
    }

    // /**
    //  * @return Shopcard[] Returns an array of Shopcard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Shopcard
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    // kullanıcı sepet ürünleri
    public function getUserShopCart($userid): array
    {
        $em=$this->getEntityManager();
        $query=$em->createQuery('
                       SELECT  p.title,p.saprice,s.quantity,s.productid,s.userid, (p.saprice*s.quantity) as total
                        FROM App\Entity\Shopcard s,App\Entity\Admin\Product p
                        WHERE  s.productid = p.id and s.userid=:userid
                       ')
            ->setParameter('userid',$userid);
         return $query->getResult();

    }

    //sepet ürünlerinin toplam tutarı için
    public function getUserShopCartTotal($userid): float
    {
        $em=$this->getEntityManager();
        $query=$em->createQuery('
                       SELECT sum(p.saprice * s.quantity) as total
                        FROM App\Entity\Shopcard s,App\Entity\Admin\Product p
                        WHERE  s.productid = p.id and s.userid=:userid
                       ')
            ->setParameter('userid',$userid);
        $result=$query->getResult();

        if($result[0]["total"]!=null)
        {
            return $result[0]["total"];
        }
        else
        {
            return 0;
        }
    }

//sepet ürünlerinin sayısı için
    public function getUserShopCartCount($userid): Integer
    {
        $em=$this->getEntityManager();
        $query=$em->createQuery('
                       SELECT count(s.id) as shopcount
                        FROM App\Entity\Shopcard s
                        WHERE s.userid=:userid
                       ')
            ->setParameter('userid',$userid);
        $result=$query->getResult();

        if($result[0]["shopcount"]!=null)
        {
            return $result[0]["shopcount"];
        }
        else
        {
            return 0;
        }
    }


}
