<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 01/10/2018
 * Time: 07:52
 */

namespace App\Tests\UseCases;

use PHPUnit\Framework\TestCase;
use App\Application\UseCases\Travel\GetAllMyTravelsService;
use App\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;
use App\Domain\User\Model\User;

class GetAllMyTravelsServiceTest extends TestCase
{
    /** @var InMemoryTravelRepository */
    private $travelRepository;

    public function setUp()
    {
        $this->travelRepository = new InMemoryTravelRepository();
        $this->travelRepository->loadData();
    }

    public function testGetAllMyTravels() {
        $user = User::fromId(1);
        $getAllMyTravelsService = new GetAllMyTravelsService($this->travelRepository);
        $travels = $getAllMyTravelsService->execute($user);

        foreach($travels as $travel) {
            $this->assertTrue($travel->getUser()->equalsTo($user));
        }

        $this->assertEquals(3,count($travels));
    }

}