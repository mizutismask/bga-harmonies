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
const MATERIAL_LOCATION_DONE = "DONE";
const MATERIAL_LOCATION_CARD = "CARD";
const MATERIAL_LOCATION_HEX = "HEX";
const MATERIAL_LOCATION_HOLE = "HOLE";
const MATERIAL_LOCATION_DISCARD = "DISCARD";
const MATERIAL_LOCATION_SPIRITS = "SPIRITS";

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
const VISIBLE_ANIMAL_CARDS_COUNT_SOLO = 3;

const EMPTIED_HOLE = "EMPTIED_HOLE";
const EMPTIED_RIVER_SLOT = "EMPTIED_RIVER_SLOT";
const TOOK_ANIMAL_CARD = "TOOK_ANIMAL_CARD";

//custom globals
const CARDS_POINTS_FOR_PLAYER = "CARD_POINTS_";

/**
 * Options
 */
define('EXPANSION', 0); // 0 => base game
define('SPIRITS', 1); // 1 => nature spirits on

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
define('CAN_RESET_TURN', 'CAN_RESET_TURN'); 

/*
    Stats
*/
//define('STAT_POINTS_WITH_PLAYER_COMPLETED_DESTINATIONS', 'pointsWithPlayerCompletedDestinations');

const TREES = GREEN;
const MOUTAINS = GRAY;
const FIELDS = YELLOW;
const BUILDINGS = RED;
const WATER = BLUE;
const ANIMAL_CARDS = 10;
