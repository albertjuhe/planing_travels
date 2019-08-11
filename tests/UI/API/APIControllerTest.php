<?php

namespace App\Tests\UI\API;

use PHPUnit\Framework\TestCase;

class APIControllerTest extends TestCase
{
    protected $endPoint;

    public function setUp()
    {
        $this->endPoint = 'http://localhost/planing_travels/public/index.php';
    }
}
