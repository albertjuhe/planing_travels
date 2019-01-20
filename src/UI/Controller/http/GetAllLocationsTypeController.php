<?php


namespace App\UI\Controller\http;

use App\Infrastructure\TypeLocationBundle\Repository\DoctrineTypeLocation;
use App\Domain\TypeLocation\Model\TypeLocation;
use App\Application\UseCases\TypeLocation\GetAllTypeLocationService;

class GetAllLocationsTypeController extends BaseController
{
    /** @var DoctrineTypeLocation */
    private $typeLocationRepository;

    /**
     * GetAllLocationsTypeController constructor.
     * @param DoctrineTypeLocation $typeLocationRepository
     */
    public function __construct(DoctrineTypeLocation $typeLocationRepository)
    {
        $this->typeLocationRepository = $typeLocationRepository;
    }

    /**
     * Get all locations type
     * @return array|mixed
     */
    public function getAllLocationsType()
    {
        $getAllTypeLocationService = new GetAllTypeLocationService($this->typeLocationRepository);
        $typeLocations = $getAllTypeLocationService->execute();

        return $this->render(
            'travel/typeLocationSelect.html.twig',
            array('typesLocation' => $typeLocations)
        );
    }
}