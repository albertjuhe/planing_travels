<?php

namespace App\Infrastructure\Application\ElasticSearch\Services;

use App\Infrastructure\TravelBundle\Exceptions\IndexDoesntExistsElasticaException;
use Elasticsearch\Common\Exceptions\InvalidArgumentException;
use FOS\ElasticaBundle\Elastica\Index;
use FOS\ElasticaBundle\Index\IndexManager;
use Elastica\Client;

class ElasticSearchIndex
{
    private $manager;
    private $client;
    private $index;

    public function __construct(
        IndexManager $manager
    ) {
        $this->manager = $manager;
        $this->client = new Client();
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
        $this->getIndex($name);

        try {
            return $this->manager->getIndex($name);
        } catch (InvalidArgumentException $e) {
            throw new IndexDoesntExistsElasticaException('Index not found.');
        }
    }

    private function getIndex(string $indexName)
    {
        $this->index = $this->client->getIndex($indexName);
        if (!$this->index->exists()) {
            $this->index->create();
        }
    }
}
