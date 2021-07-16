<?php
/*
 * This file is part of the basic-pokeapi package.
 *
 * (c) Valérie Tiberghien
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hb\BasicPokeapi;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class Pokedex
 *
 * @author Valérie Tiberghien
 */
class Pokedex
{
    private HttpClientInterface $client;
    //private array $datas;

    /**
     * Pokedex constructor.
     */
    public function __construct()
    {
        $this->client = HttpClient::createForBaseUri('https://pokeapi.co/api/v2/');
        //$this->datas = [];
    }

    public function getAllPokemon(int $offset=0): array
    {

        $response = $this->client->request('GET', 'pokemon', [
            'query' => [
                'offset' => $offset,
            ],
        ]);
        if(200 !==$response->getStatusCode()) {
            throw new \RuntimeExceptio('Error from PokeApi.co');
        }
        $data = $response->toArray();
        $pokemons = [];

        //'id' =>explode('/',explode('https://pokeapi.co/api/v2/pokemon/', $pokemon['url'])[1])[0],

        foreach($data['results'] as $pokemon) {
            if (!preg_match('/([0-9]+)\/?$/', $pokemon['url'], $matches)) {
                throw new \RuntimeExceptio('Cannot match given url for pokemon ' . $pokemon['name']);
            }
            $id=$matches[1];
            $pokemons[] = [
                'id' => $id,
                'name' => $pokemon['name'],
            ];
        }

       //next page
        if ($data['next']) {
            if (!preg_match('/\?.*offset=([0-9]+)/', $data['next'], $matches)) {
                throw new \RuntimeException('Cannot match offset on next page.');
            }

            $nextOffset = $matches[1];

            $nextPokemons = $this->getAllPokemon($nextOffset);
            $pokemons = array_merge($pokemons, $nextPokemons);
        }
        return $pokemons;

    }



    /**
     * @return array
     */
    public function getPikachu(): array
    {
        $response = $this->client->request('GET', 'pokemon/25');

        if(200 !==$response->getStatusCode()) {
            throw new \RuntimeExceptio('Error from PokeApi.co');
        }

        return $response->toArray();
    }

    /**
     * @return array
     */
    public function getCleanPikachu(): array
    {
        $data = $this->getPikachu();

        $clean = array_intersect_key($data, array_flip(['id', 'name', 'weight', 'base_experience']));
        $clean['image'] = $data['sprites']['front_default'];

        return $clean;

        /*return [
            'id' => $data['id'],
            'name' => $data['name'],
            'weight' => $data['weight'],
            'base_experience' => $data['base_experience'],
            'image' => $data['sprites']['front_default'],
        ];*/
    }

}