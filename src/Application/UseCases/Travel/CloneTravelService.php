<?php

namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\CloneTravelCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Gpx\Model\Gpx;
use App\Domain\Images\Model\Images;
use App\Domain\Location\Model\Location;
use App\Domain\Location\Model\LocationVisitDate;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Model\TravelClone;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class CloneTravelService implements UsesCasesService
{
    private TravelRepository $travelRepository;
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(
        TravelRepository $travelRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ) {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function __invoke(CloneTravelCommand $command): Travel
    {
        $source = $this->travelRepository->ofIdOrFail($command->getSourceTravelId());
        $user = $this->userRepository->ofIdOrFail(new UserId($command->getUserId()));

        if (!$this->canClone($source, $user)) {
            throw new \RuntimeException('Not allowed to clone this travel.');
        }

        $clone = Travel::cloneFrom($source, $user, $command->getNewTitle(), $command->isCopyGpx());

        $slugger = new AsciiSlugger();
        $baseSlug = strtolower((string) $slugger->slug($clone->getTitle()));
        $clone->setSlug($baseSlug . '-' . substr($clone->getId()->id(), 0, 8));

        foreach ($source->getLocation() as $srcLocation) {
            $newLocation = $this->cloneLocation($srcLocation, $clone);
            $this->em->persist($newLocation);
        }

        if ($command->isCopyGpx()) {
            foreach ($source->getGpx() as $srcGpx) {
                $newGpx = $this->cloneGpx($srcGpx, $clone);
                $this->em->persist($newGpx);
            }
        }

        $depth = 1;
        if ($source->getClonedFromTravelId() !== null) {
            $depth = 2;
        }

        $travelClone = new TravelClone(
            $source->getId()->id(),
            $source->getUser()->getId()->id(),
            $source->getTitle(),
            $clone,
            $user,
            $depth
        );

        $source->incrementCloneCount();
        $source->recordCloned($clone->getId()->id(), $user->getId()->id());

        $this->travelRepository->save($clone);
        $this->travelRepository->save($source);
        $this->em->persist($travelClone);

        return $clone;
    }

    private function canClone(Travel $travel, $user): bool
    {
        if ($travel->isPublished()) {
            return true;
        }
        if ($travel->getUser()->getId()->id() === $user->getId()->id()) {
            return true;
        }
        foreach ($travel->getSharedusers() as $shared) {
            if ($shared->getId()->id() === $user->getId()->id()) {
                return true;
            }
        }

        return false;
    }

    private function cloneLocation(Location $src, Travel $newTravel): Location
    {
        $loc = new Location();
        $loc->setTitle($src->getTitle());
        $loc->setDescription($src->getDescription());
        $loc->setUrl($src->getUrl());
        $loc->setStars($src->getStars());
        $loc->setTravel($newTravel);

        if ($src->getMark()) {
            $loc->setMark($src->getMark());
        }
        try {
            if ($src->getTypeLocation()) {
                $loc->setTypeLocation($src->getTypeLocation());
            }
        } catch (\Throwable $e) {
            // typeLocation may be null on old locations
        }

        foreach ($src->getVisitDates() as $vd) {
            $newVd = new LocationVisitDate($loc, clone $vd->getVisitDate());
            $newVd->setPosition($vd->getPosition());
            $loc->getVisitDates()->add($newVd);
            $this->em->persist($newVd);
        }

        foreach ($src->getImages() as $img) {
            $clonedImg = Images::cloneReference($img, $loc);
            $loc->addImages($clonedImg);
            $this->em->persist($clonedImg);
        }

        return $loc;
    }

    private function cloneGpx(Gpx $src, Travel $newTravel): Gpx
    {
        $gpx = new Gpx();
        $gpx->setTitle($src->getTitle());
        $gpx->setDescription($src->getDescription());
        $gpx->setFilename($src->getFilename());
        $gpx->setColor($src->getColor());
        $gpx->setTravel($newTravel);

        return $gpx;
    }
}
