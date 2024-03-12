<?php

//require_once(__DIR__ . '/objects/coloredToken.php');

trait HoneycombTrait {
    public function getHexesCoordinates() {
        $hexes = [];
        for ($i = 0; $i < $this->getBoardWidth(); $i++) {
            for ($j = 0; $j < $this->getBoardHeight(); $j++) {
                if ($i % 2 == 1 && $j == $this->getBoardHeight() - 1) {
                } else {
                    $hexes[] = ["col" => $i, "row" => $j];
                }
            }
        }
        //self::dump('*******************hexes', $hexes);
        return $hexes;
    }

    public function getBoard($playerId) {
        $coords = $this->getHexesCoordinates();
        $existingTokens = $this->getTokensForCompleteBoardByHex($playerId);
        foreach ($coords as &$hex) {
            $cellName = $this->convertHexCoordsToName($hex, $playerId);
            if (isset($existingTokens[$cellName]))
                $hex["tokens"] = $existingTokens[$cellName];
            else $hex["tokens"] = [];
        }
        //self::dump('************** *****getBoard', $coords);
        return $coords;
    }
}
