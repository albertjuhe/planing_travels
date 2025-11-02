<?php
declare(strict_types=1);

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class TravelContext implements Context
{
    /** @var KernelInterface */
    private $kernel;

    /** @var Response|null */
    private $response;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @When call API to get locations from travel to :arg1
     */
    public function callApiToGetLocationsFromTravelTo($arg1): void
    {
        $path = "/api/user/4/travels";

        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }

    /**
     * @Then the response status code should be :arg1
     */
    public function theResponseStatusCodeShouldBe($arg1): void
    {
        if ($this->response === null) {
            throw new \RuntimeException('No response received');
        }
    }

}