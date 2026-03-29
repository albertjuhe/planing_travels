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
        IndexManager $manager,
        Client $client
    ) {
        $this->manager = $manager;
        $this->client = $client;
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
            $this->index->create([
                'mappings' => [
                    'properties' => [
                        'id'          => ['type' => 'keyword'],
                        'title'       => ['type' => 'text'],
                        'description' => ['type' => 'text'],
                        'slug'        => ['type' => 'keyword'],
                        'stars'       => ['type' => 'integer'],
                        'watch'       => ['type' => 'integer'],
                        'status'      => ['type' => 'integer'],
                        'userId'      => ['type' => 'keyword'],
                        'username'    => ['type' => 'keyword'],
                        'photo'       => ['type' => 'keyword'],
                        'publishedAt' => ['type' => 'date'],
                        'createdAt'   => ['type' => 'date'],
                        'updatedAt'   => ['type' => 'date'],
                        'startAt'     => ['type' => 'date'],
                        'endAt'       => ['type' => 'date'],
                        'latitude'    => ['type' => 'float'],
                        'longitud'    => ['type' => 'float'],
                    ],
                ],
            ]);
        }
    }
}
