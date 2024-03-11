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
 * Location : deck, centralBoard or playerId_hexId (board)
 * Location arg : holeNumber (in centralBoard), zindex (in board)
 * Type : 1 for simple ColoredToken
 * Type arg : the ColoredToken color
 */
class ColoredToken extends ColoredTokenInfo {
    public int $id;
    public string $location;
    public int $location_arg;
    public int $type;
    public int $type_arg;
    public bool $done = false;

    public function __construct($dbCard) {
        array_key_exists('id', $dbCard) ? $this->id = intval($dbCard['id']):null;
        array_key_exists('location', $dbCard) ? $this->location = $dbCard['location']:null;
        array_key_exists('location_arg', $dbCard) ? $this->location_arg = intval($dbCard['location_arg']):null;
        array_key_exists('type', $dbCard) ? $this->type = intval($dbCard['type']):null;
        array_key_exists('type_arg', $dbCard) ? $this->type_arg = intval($dbCard['type_arg']):null;
    }
}
