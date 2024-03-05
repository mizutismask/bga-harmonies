<?php

/**
 * A ColoredTokenInfo is the graphic representation of a token (with additional informations on it).
 */
class ColoredTokenInfo {

    public function __construct() {
    }
}

/**
 * A ColoredToken is a physical token. It contains informations from matching ColoredTokenInfo, with technical informations like id and location.
 * Location : centralBoard or playerBoard (board)
 * Location arg : holeNumber (in centralBoard), playerId_cell_zindex (in board)
 * Type : 1 for simple ColoredToken
 * Type arg : the ColoredToken color
 */
class ColoredToken extends ColoredTokenInfo {
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
