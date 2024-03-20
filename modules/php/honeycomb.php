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
            if (isset($existingTokens[$cellName])) {
                $hex["tokens"] = $existingTokens[$cellName];
                $hex["topToken"] = isset($existingTokens[$cellName][0]) ? $existingTokens[$cellName][0] : null;
            } else {
                $hex["tokens"] = [];
                $hex["topToken"] = null;
            }
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

    function getHexIndexInBoard($board, $hexCol, $hexRow) {
        $hex = array_filter($board, fn ($eh) => $this->hexesEquals($eh, $hexCol, $hexRow));
        return array_keys($hex)[0];
    }

    function hexesEquals($hex,  $hexCol, $hexRow) {
        return $hex["col"] == $hexCol && $hex["row"] == $hexRow;
    }

    function isHexInZone(array $hex, array $hexesZone) {
        $found = array_filter($hexesZone, fn ($eh) => $this->hexesEquals($eh, $hex["col"], $hex["row"]));
        return count($found) > 0;
    }

    function getPossibleLocationsForCubeInPattern($board, AnimalCardInfo $card, $convertNames = false, $playerId="") {
        $possible = [];
        foreach ($board as $hex) {
            $allHexesValid = true;
            foreach ($card->pattern as $hexPattern) {
                //self::dump('******************$hexPattern*', $hexPattern);
                $expected = $this->areExpectedTokensInHex($board, $hex["col"] + $hexPattern->shiftCol, $hex["row"] + $hexPattern->shiftRow, $hexPattern->colors);
                //self::dump('******************$expected*', $expected);
                $allHexesValid = $allHexesValid && $expected;
                if (!$allHexesValid) {
                    break;
                }
            }
            if ($allHexesValid) {
                $cubeLocation = array_values(array_filter($card->pattern, fn ($hexPattern) => $hexPattern->allowCube === true))[0];
                $cubeHex = ["col" => $hex["col"] + $cubeLocation->shiftCol, "row" => $hex["row"] + $cubeLocation->shiftRow];
                $possible[] = $convertNames ? $this->convertHexCoordsToName($cubeHex, $playerId) : $cubeHex;
            }
        }
        return $possible;
    }

    function areExpectedTokensInHex($board, int $col, int $row, $expectedColors) {
        $valid = false;
        if ($this->containsHex($board, $col, $row)) {
            $hex = $board[$this->getHexIndexInBoard($board, $col, $row)];
            $hexTokens = $hex["tokens"];
            //if ($hexTokens)
                //self::dump('******************$hexTokens*', $hexTokens);
            if (count($hexTokens) === count($expectedColors)) {
                $valid = true;
                //check if colors are at the expected level
                for ($i = 0; $i < count($expectedColors); $i++) {
                    $valid = $valid && ($hexTokens[$i]->type_arg === $expectedColors[$i]
                        || ($expectedColors[$i] === BUILDING && in_array($hexTokens[$i]->type_arg, [RED, BROWN, GRAY])));
                }
            }
        }
        return $valid;
    }
}
