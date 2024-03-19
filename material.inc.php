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


$this->ANIMAL_CARDS = [
  1 => [
    1 => new AnimalCardInfo([15, 9, 4], [new PatternHex([BROWN, BROWN, GREEN], 0, 0, false), new PatternHex([BLUE], 1, 0, false), new PatternHex([BLUE], 2, 1, true)]),
    2 => new AnimalCardInfo([16, 10, 4], [new PatternHex([GRAY], 0, 0, false), new PatternHex([GRAY], 0, 1, false), new PatternHex([BLUE], 1, 0, true)]),
    3 => new AnimalCardInfo([16, 10, 6, 3], [new PatternHex([GRAY, GRAY, GRAY], 0, 0, false), new PatternHex([BLUE], 1, 0, true)]),
    4 => new AnimalCardInfo([16, 10, 5], [new PatternHex([GREEN], 0, 0, false), new PatternHex([GREEN], 1, 0, false), new PatternHex([BLUE], 2, 0, true)]),
    5 => new AnimalCardInfo([15, 10, 6, 4,2], [new PatternHex([ GREEN], 0, 0, false),  new PatternHex([BLUE], 1, 0, true)]),
    6 => new AnimalCardInfo([13, 8, 4,2], [new PatternHex([BUILDING, RED], 0, 0, false), new PatternHex([BLUE], 1, 0, true)]),
  ]
];
