<?php


namespace App\Infrastructure\Application\ElasticSearch\Services;

use FOS\ElasticaBundle\Elastica\Index;
use FOS\ElasticaBundle\Index\IndexManager;

class ElasticSearchIndex
{
    private $manager;

    public function __construct(IndexManager $manager)
    {
        $this->manager = $manager;
    }

    public function getOne(string $name): Index
    {
        return $this->find($name);
    }

    public function getAll(): iterable
    {
        return $this->manager->getAllIndexes();
    }

    public function getAliasName(string $name): string
    {
        return $this->find($name)->getName();
    }

    private function find(string $name): Index
    {
        try {
            return $this->manager->getIndex($name);
        } catch (InvalidArgumentException $e) {
            throw new ElasticaException('Index not found.');
        }
    }
}