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

    public function getNeighbours($hex) {
        $x = (int) $hex['col'];
        $y = (int) $hex['row'];
        $hexes = [];
        $hexes[] = ['col' => $x, 'row' => $y - 1];
        $hexes[] = ['col' => $x - 1, 'row' => $y + ($x % 2 == 0 ? -1 : 0)];
        $hexes[] = ['col' => $x - 1, 'row' => $y + ($x % 2 == 0 ? 0 : 1)];
        $hexes[] = ['col' => $x, 'row' => $y + 1];
        $hexes[] = ['col' => $x + 1, 'row' => $y + ($x % 2 == 0 ? 0 : 1)];
        $hexes[] = ['col' => $x + 1, 'row' => $y + ($x % 2 == 0 ? -1 : 0)];

        $hexes = array_filter($hexes, fn ($hex) => $this->isValidHex($hex));
        $hexes = array_values($hexes);
        return $hexes;
    }

    function isValidHex($hex) {
        $existingHexes = $this->getHexesCoordinates();
        return $this->array_some($existingHexes, fn ($eh) => $eh["col"] == $hex["col"] && $eh["row"] == $hex["row"]);
    }

    function containsHex($hexes, $hexCol, $hexRow) {
        return $this->array_some($hexes, fn ($eh) => $this->hexesEquals($eh, $hexCol, $hexRow));
    }

    function getHex($board, $hexCol, $hexRow) {
        return array_filter($board, fn ($eh) => $this->hexesEquals($eh, $hexCol, $hexRow));
    }

    function hexesEquals($hex,  $hexCol, $hexRow) {
        return $hex["col"] == $hexCol && $hex["row"] == $hexRow;
    }
}
