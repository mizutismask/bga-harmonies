<?php

/**
 * A AnimalCubeInfo is the graphic representation of a token (with additional informations on it).
 */
class AnimalCubeInfo {

    public function __construct() {
    }
}

/**
 * A AnimalCube is a physical token. It contains informations from matching AnimalCubeInfo, with technical informations like id and location.
 * Location : deck or card_cardId or hex_hexId
 * Location arg : scoreLocation (in animalCard_cardId)
 * Type : 1 
 * Type arg : not used
 */
class AnimalCube extends AnimalCubeInfo {
    public int $id;
    public string $location;
    public int $location_arg;
    public int $type;
    public int $type_arg;

    public function __construct($dbCard) {
        $this->id = intval($dbCard['id']);
        $this->location = $dbCard['location'];
        $this->location_arg = intval($dbCard['location_arg']);
        $this->type = intval($dbCard['type']);
        $this->type_arg = intval($dbCard['type_arg']);
    }
}
