<?php

/**
 * An AnimalCardInfo is the graphic representation of a card (informations on it : pattern, points locations, animal required location…).
 */
class AnimalCardInfo {
    public array $pointLocations;
    public array $pattern; //array of PatternHex

    public function __construct(array $pointLocations, array $pattern) {
        $this->pointLocations = $pointLocations;
        $this->pattern = $pattern;
    }
}

class PatternHex {
    public array $colors; //from top to bottom
    public int $shiftCol; //relative the first hex in the pattern, named 0,0
    public int $shiftRow;
    public bool $allowCube;

    public function __construct(array $colors, $shiftCol, $shiftRow, $allowCube) {
        $this->colors = $colors;
        $this->shiftCol = $shiftCol;
        $this->shiftRow = $shiftRow;
        $this->allowCube = $allowCube;
    }
}

/**
 * A AnimalCard is a physical card. It contains informations from matching AnimalCardInfo, with technical informations like id and location.
 * Location : deck, river or board_playerId
 * Location arg : order (in deck or river), column (in board_playerId)
 * Type : 1
 * Type arg : the animalCard type (AnimalCardInfo id)
 */
class AnimalCard extends AnimalCardInfo {
    public int $id;
    public string $location;
    public int $location_arg;
    public int $type;
    public int $type_arg;

    public function __construct($dbCard, $ANIMAL_CARDS) {
        $this->id = intval($dbCard['id']);
        $this->location = $dbCard['location'];
        $this->location_arg = intval($dbCard['location_arg']);
        $this->type = intval($dbCard['type']);
        $this->type_arg = intval($dbCard['type_arg']);
        $animalCardInfo = $ANIMAL_CARDS[$this->type][$this->type_arg];
        $this->pointLocations = $animalCardInfo->pointLocations;
        $this->pattern = $animalCardInfo->pattern;
    }
}
