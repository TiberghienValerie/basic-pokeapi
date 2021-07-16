<?php
/*
 * This file is part of the basic-pokeapi package.
 *
 * (c) Valérie Tiberghien
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once __DIR__ . '/../vendor/autoload.php';

$pokedex = new \Hb\BasicPokeapi\Pokedex();

header('Content-Type: application/json');


if (!isset($_SERVER['PATH_INFO']) || '/pokemon' === $_SERVER['PATH_INFO']) {
    // Query data with Pokedex and convert to JSON to be send to the client.
    echo json_encode($pokedex->getAllPokemon());
} elseif ('/pikachu' === $_SERVER['PATH_INFO']) {
    echo json_encode($pokedex->getCleanPikachu());
} else {
    http_response_code(404);
    echo json_encode([
        'error' => 'Wrong path.',
    ]);
}