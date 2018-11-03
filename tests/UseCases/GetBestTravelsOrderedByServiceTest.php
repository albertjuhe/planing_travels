<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 01/10/2018
 * Time: 08:46
 */

namespace App\Tests\UseCases;


class GetBestTravelsOrderedByServiceTest
{
    /** @var InMemoryTravelRepository */
    private $travelRepository;

    public function setUp()
    {
        $this->travelRepository = new InMemoryTravelRepository();
        $this->travelRepository->loadData();
    }


    public function testGetBestTravelsOrderedByService()
    {

    }

}