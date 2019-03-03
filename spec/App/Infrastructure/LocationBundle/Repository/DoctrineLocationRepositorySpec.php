<?php

namespace spec\App\Infrastructure\LocationBundle\Repository;

use App\Domain\Location\Model\Location;
use App\Domain\Location\Repository\LocationRepository;
use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Doctrine\ORM\Mapping\ClassMetadata;

class DoctrineLocationRepositorySpec extends ObjectBehavior
{
    public function let(ManagerRegistry $registry)
    {
        $this->beConstructedWith($registry);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DoctrineLocationRepository::class);
        $this->shouldHaveType(ServiceEntityRepository::class);
        $this->shouldHaveType(LocationRepository::class);
    }

    public function it_saves(
        ManagerRegistry $registry,
        Location $location,
        EntityManagerInterface $em
    ) {
        $class = new ClassMetadata(Location::class);

        $location->__toString()->willReturn('location');
        $registry->getManagerForClass(Location::class)->willReturn($em);
        $em->getClassMetadata(Location::class)->willReturn($class);
        $em->persist($location)->shouldBeCalledTimes(1);
        $this->save($location);
    }
}
