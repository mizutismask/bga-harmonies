<?php

/*
 * BGA constants 
 */
define("GS_PLAYER_TURN_NUMBER", 'playerturn_nbr');

/*
 * Custom framework constants
 */
const MATERIAL_TYPE_CARD = "CARD";
const MATERIAL_TYPE_CUBE = "CUBE";
const MATERIAL_TYPE_TOKEN = "TOKEN";
const MATERIAL_TYPE_FIRST_PLAYER_TOKEN = "FIRST_PLAYER_TOKEN";

const MATERIAL_LOCATION_HAND = "HAND";
const MATERIAL_LOCATION_DECK = "DECK";
const MATERIAL_LOCATION_STOCK = "STOCK";
const MATERIAL_LOCATION_RIVER = "RIVER";
const MATERIAL_LOCATION_CARD = "CARD";
const MATERIAL_LOCATION_HEX = "HEX";

/* 
 * Game constants 
 */
const BLUE = 1;
const GRAY = 2;
const BROWN = 3;
const GREEN = 4;
const YELLOW = 5;
const RED = 6;
const BUILDING = 7;//stands for either gray, red or brown
const VISIBLE_ANIMAL_CARDS_COUNT = 5;

const EMPTIED_HOLE = "EMPTIED_HOLE";
const TOOK_ANIMAL_CARD = "TOOK_ANIMAL_CARD";

/**
 * Options
 */
define('EXPANSION', 0); // 0 => base game

/*
 * State constants
 */
define('ST_BGA_GAME_SETUP', 1);
define('ST_DEAL_INITIAL_SETUP', 10);

define('ST_PLAYER_CHOOSE_ACTION', 30);

define('ST_NEXT_PLAYER', 80);
define('ST_NEXT_REVEAL', 81);

define('ST_DEBUG_END_GAME', 97);
define('ST_END_SCORE', 98);

define('ST_END_GAME', 99);
define('END_SCORE', 100);


/*
 * Variables (numbers)
 */

define('LAST_TURN', 'LAST_TURN');


/*
 * Global variables (objects)
 */
define('TOKENS_IN_HOLE', 'TOKENS_IN_HOLE'); //chosen colored tokens during mandatory action

/*
    Stats
*/
//define('STAT_POINTS_WITH_PLAYER_COMPLETED_DESTINATIONS', 'pointsWithPlayerCompletedDestinations');

const TREES = "TREES";
const MOUTAINS = "MOUTAINS";
const FIELDS = "FIELDS";
const BUILDINGS = "BUILDINGS";
const WATER = "WATER";
const ANIMAL_CARDS = "ANIMAL_CARDS";
function getScoresTypes() {
    return [
        ["type" => TREES, "stat" => "game_score_trees", "nameTr" => clienttranslate("trees")],
        ["type" => MOUTAINS, "stat" => "game_score_mountains", "nameTr" => clienttranslate("mountains")],
        ["type" => FIELDS, "stat" => "game_score_fields", "nameTr" => clienttranslate("fields")],
        ["type" => BUILDINGS, "stat" => "game_score_buildings", "nameTr" => clienttranslate("buildings")],
        ["type" => WATER, "stat" => "game_score_water", "nameTr" => clienttranslate("water")],
        ["type" => ANIMAL_CARDS, "stat" => "game_animal_cards_score", "nameTr" => clienttranslate("animal cards")],
    ];
}
