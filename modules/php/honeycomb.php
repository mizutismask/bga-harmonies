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
        $hexes[] = ['col' => $x, 'row' => $y - 1]; //top
        $hexes[] = ['col' => $x - 1, 'row' => $y + ($x % 2 == 0 ? -1 : 0)]; //top left
        $hexes[] = ['col' => $x - 1, 'row' => $y + ($x % 2 == 0 ? 0 : 1)]; //bottom left
        $hexes[] = ['col' => $x, 'row' => $y + 1]; //bottom
        $hexes[] = ['col' => $x + 1, 'row' => $y + ($x % 2 == 0 ? 0 : 1)]; //bottom right
        $hexes[] = ['col' => $x + 1, 'row' => $y + ($x % 2 == 0 ? -1 : 0)]; //top right

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

    function getPossibleLocationsForCubeInPattern($board, AnimalCardInfo $card, $convertNames = false, $playerId = "") {
        $possible = [];
        foreach ($board as $hex) {
            $rotatedPatterns = $this->getPatternRotations($card->pattern);
            //self::dump('******************$rotatedPatterns*', $rotatedPatterns);
            foreach ($rotatedPatterns as $patternRotation) {
                $allHexesValid = true;

                $previousCheckedHex = null;
                //self::dump('******************$patternRotation*', $patternRotation);
                foreach ($patternRotation as $hexPattern) {
                    //self::dump('******************$hexPattern*', $hexPattern);
                    if ($hexPattern->position == 0) {
                        $previousCheckedHex = $hex;
                    } else {
                        $previousCheckedHex = $this->getAdjacentHexCoordinate($previousCheckedHex, $hexPattern->position);
                    }
                    $expected = $this->areExpectedTokensInHex($board, $previousCheckedHex["col"], $previousCheckedHex["row"], $hexPattern->colors);
                    //self::dump('******************$expected*', $expected);
                    $allHexesValid = $allHexesValid && $expected;
                    if (!$allHexesValid) {
                        break;
                    }
                }
                if ($allHexesValid) {
                    $cubeHex = $this->getCubeCoordinate($patternRotation, $hex);
                    $possible[] = $convertNames ? $this->convertHexCoordsToName($cubeHex, $playerId) : $cubeHex;
                }
            }
        }
        return $possible;
    }

    function getCubeCoordinate($patternRotation, $firstHexInPattern) {
        $found = false;
        $i = 0;
        $hex = null;
        while (!$found && $i < count($patternRotation)) {
            $pattern = $patternRotation[$i];
            if ($pattern->position == 0) {
                $hex = $firstHexInPattern;
            } else {
                $hex = $this->getAdjacentHexCoordinate($hex, $pattern->position);
            }
            $found = ($pattern->allowCube === true);
            $i++;
        }
        if (!$found) {
            throw new BgaSystemException("No cube position defined in card");
        }
        return $hex;
    }

    public function getAdjacentHexCoordinate($hex, $numHex) {
        $x = (int) $hex['col'];
        $y = (int) $hex['row'];

        switch ($numHex) {
            case 1: // top
                $newCol = $x;
                $newRow = $y - 1;
                break;
            case 6: // top left
                $newCol = $x - 1;
                $newRow = $y + ($x % 2 == 0 ? -1 : 0);
                break;
            case 5: // bottom left
                $newCol = $x - 1;
                $newRow = $y + ($x % 2 == 0 ? 0 : 1);
                break;
            case 4: // bottom
                $newCol = $x;
                $newRow = $y + 1;
                break;
            case 3: // bottom right
                $newCol = $x + 1;
                $newRow = $y + ($x % 2 == 0 ? 0 : 1);
                break;
            case 2: // top right
                $newCol = $x + 1;
                $newRow = $y + ($x % 2 == 0 ? -1 : 0);
                break;
            default:
                throw new BgaSystemException("Hex number invalid, should be between 1 and 6 :" . $numHex);
        }

        return  ["col" => $newCol, "row" => $newRow];
    }

    function getPatternRotations(array $patternHexList) {
        $rotations = [];
        $numHexes = count($patternHexList);

        for ($rotation = 0; $rotation < 6; $rotation++) {
            $rotatedHexes = [];
            for ($i = 0; $i < $numHexes; $i++) {
                $patternHex = $patternHexList[$i];
                if ($patternHex->position == 0) {
                    $rotatedHexes[] = $patternHex;
                } else {
                    $newPosition = ($patternHex->position + $rotation) % 6;
                    if ($newPosition == 0) {
                        $newPosition = 6;
                    }
                    $rotatedHexes[] = new PatternHex($patternHex->colors, $newPosition, $patternHex->allowCube);
                }
            }
            $rotations[] = $rotatedHexes;
        }

        return $rotations;
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
