<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Harmonies implementation : © Séverine Kamycki <mizutismask@gmail.com>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * Harmonies game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

//piles are from top to bottom
$this->ANIMAL_CARDS = [
  1 => [
    1 => new AnimalCardInfo([15, 9, 4], [new PatternHex([GREEN, BROWN, BROWN], 0, false), new PatternHex([BLUE], 3, false), new PatternHex([BLUE], 3, true)]),
    2 => new AnimalCardInfo([16, 10, 4], [new PatternHex([GRAY], 0, false), new PatternHex([GRAY], 4, false), new PatternHex([BLUE], 2, true)]),
    3 => new AnimalCardInfo([16, 10, 6, 3], [new PatternHex([GRAY, GRAY, GRAY], 0, false), new PatternHex([BLUE], 3, true)]),
    4 => new AnimalCardInfo([16, 10, 5], [new PatternHex([GREEN], 0, false), new PatternHex([GREEN], 3, false), new PatternHex([BLUE],  3, true)]),
    5 => new AnimalCardInfo([15, 10, 6, 4, 2], [new PatternHex([GREEN], 0, false),  new PatternHex([BLUE], 3, true)]),
    6 => new AnimalCardInfo([13, 8, 4, 2], [new PatternHex([RED, BUILDING,], 0,  false), new PatternHex([BLUE], 3, true)]),
    7 => new AnimalCardInfo([16, 10, 4], [new PatternHex([YELLOW], 0,  false), new PatternHex([BLUE], 3, true), new PatternHex([YELLOW], 5,  false)]),
    8 => new AnimalCardInfo([16, 10, 5], [new PatternHex([YELLOW,], 0,  false), new PatternHex([YELLOW], 3, false), new PatternHex([RED, BUILDING,], 3,  true)]),
    9 => new AnimalCardInfo([17, 10, 5], [new PatternHex([YELLOW], 0,  false), new PatternHex([RED, BUILDING], 2, true), new PatternHex([YELLOW], 3, false)]),
    10 => new AnimalCardInfo([17, 10, 5], [new PatternHex([BLUE], 0,  false), new PatternHex([RED, BUILDING], 2, true), new PatternHex([BLUE], 3, false)]),

    11 => new AnimalCardInfo([15, 9, 4], [new PatternHex([GREEN, BROWN, BROWN], 0,  false), new PatternHex([RED, BUILDING], 3, true)]),
    12 => new AnimalCardInfo([12, 5], [new PatternHex([GREEN, BROWN], 0,  false), new PatternHex([RED, BUILDING], 3, true), new PatternHex([GREEN, BROWN], 5, false)]),
    13 => new AnimalCardInfo([18, 8], [new PatternHex([YELLOW], 0,  false), new PatternHex([GREEN, BROWN], 2, true), new PatternHex([YELLOW], 4, false), new PatternHex([YELLOW], 2, false)]),
    14 => new AnimalCardInfo([11,  5], [new PatternHex([GRAY, GRAY], 0,  false), new PatternHex([GREEN], 3, true), new PatternHex([GRAY, GRAY], 5, false)]),
    15 => new AnimalCardInfo([17, 10, 5], [new PatternHex([RED, BUILDING], 0,  false), new PatternHex([GREEN], 3, false), new PatternHex([GREEN], 3, true)]),
    16 => new AnimalCardInfo([14, 9, 4], [new PatternHex([BLUE], 0,  false), new PatternHex([GREEN, BROWN], 3, true), new PatternHex([BLUE], 5, false)]),
    17 => new AnimalCardInfo([13, 8, 4], [new PatternHex([RED, BUILDING], 0,  false), new PatternHex([GREEN, BROWN], 3, true)]),
    18 => new AnimalCardInfo([15, 10, 6, 3], [new PatternHex([GREEN], 0,  false), new PatternHex([GREEN, BROWN], 3, true)]),
    19 => new AnimalCardInfo([16, 10, 4], [new PatternHex([YELLOW], 0,  false), new PatternHex([GREEN, BROWN, BROWN], 3, true), new PatternHex([YELLOW], 5, false)]),
    20 => new AnimalCardInfo([18, 11, 5], [new PatternHex([BLUE], 0,  false), new PatternHex([GREEN, BROWN, BROWN], 2, true), new PatternHex([BLUE], 3, false)]),

    21 => new AnimalCardInfo([16, 10, 4], [new PatternHex([BLUE], 0,  false), new PatternHex([GRAY], 2, true), new PatternHex([BLUE], 3, false)]),
    22 => new AnimalCardInfo([15, 10, 6, 3], [new PatternHex([GREEN, BROWN, BROWN], 0,  false), new PatternHex([GRAY], 3, true)]),
    23 => new AnimalCardInfo([16, 9, 4], [new PatternHex([YELLOW], 0,  false), new PatternHex([GRAY], 3, false), new PatternHex([GRAY], 3, true)]),
    24 => new AnimalCardInfo([11, 5], [new PatternHex([BLUE], 0,  false), new PatternHex([GRAY, GRAY], 3, true), new PatternHex([BLUE], 5, false)]),
    25 => new AnimalCardInfo([11, 5], [new PatternHex([YELLOW], 0,  false), new PatternHex([GRAY, GRAY, GRAY], 3, true)]),
    26 => new AnimalCardInfo([14, 9, 5, 2], [new PatternHex([YELLOW], 0,  false), new PatternHex([GRAY], 3, true)]),
    27 => new AnimalCardInfo([9, 4], [new PatternHex([RED, BUILDING], 0,  false), new PatternHex([YELLOW], 2, true), new PatternHex([RED, BUILDING], 3, false)]),
    28 => new AnimalCardInfo([12, 5], [new PatternHex([GRAY, GRAY], 0,  false), new PatternHex([YELLOW], 3, false), new PatternHex([YELLOW], 3, true)]),
    29 => new AnimalCardInfo([17, 10, 5], [new PatternHex([GREEN, BROWN], 0,  false), new PatternHex([YELLOW], 2, true), new PatternHex([GREEN, BROWN], 3, false)]),
    30 => new AnimalCardInfo([12, 6], [new PatternHex([BLUE], 0,  false), new PatternHex([YELLOW], 2, true), new PatternHex([BLUE], 4, false), new PatternHex([BLUE], 2, false)]),

    31 => new AnimalCardInfo([17, 12, 8, 5, 2], [new PatternHex([GREEN], 0,  false), new PatternHex([YELLOW], 3, true)]),
    32 => new AnimalCardInfo([11, 5], [new PatternHex([GREEN, BROWN], 0,  false), new PatternHex([GREEN, BROWN], 3, false), new PatternHex([YELLOW], 3, true)]),
  ]
];
