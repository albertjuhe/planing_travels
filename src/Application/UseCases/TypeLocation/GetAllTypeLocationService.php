<?php


namespace App\Application\UseCases\TypeLocation;

use App\Domain\TypeLocation\Repository\TypeLocationRepository;

class GetAllTypeLocationService
{
    /** @var TypeLocationRepository */
    private $typeLocationRepository;

    /**
     * GetAllTypeLocationService constructor.
     * @param TypeLocationRepository $typeLocationRepository
     */
    public function __construct(TypeLocationRepository $typeLocationRepository)
    {
        $this->typeLocationRepository = $typeLocationRepository;
    }

    public function execute() {
        return $this->typeLocationRepository->getAllTypeLocations();
    }


}