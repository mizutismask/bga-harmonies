<?php

require_once(__DIR__ . '/objects/animalCard.php');
trait ScoreTrait {
    static $treePoints = [1, 3, 7];
    public function calculateTreePoints($board) {
        $total = 0;
        foreach ($board as $hex) {
            $tokens = $this->getTokensAt($hex);
            $topToken = array_shift($tokens);
            if ($topToken && $topToken->type_arg == GREEN) {
                $total += self::$treePoints[count($tokens)];
            }
        }
        return $total;
    }

    public function calculateBuildingPoints($board) {
        $total = 0;
        foreach ($board as $hex) {
            $tokens = $hex["tokens"];
            if ($this->isBuilding($tokens)) {
                if ($this->isHexSurroundedBy3DifferentColors($board, $hex))
                    $total += 5;
            }
        }
        return $total;
    }

    public function isHexSurroundedBy3DifferentColors($board, $hex) {
        $goal = 3;
        $neighbours = $this->getNeighbours($hex);
        $colors = [];
        $i = 0;
        while (count($colors) < $goal && $i < count($neighbours)) {
            $neighb = $neighbours[$i];
            $top = $this->getTopTokenAtHexFromBoard($board, $neighb);
            if ($top) {
                $colors[] = $top->type_arg;
            }
            $i++;
        }
        return count(array_unique($colors)) == $goal;
    }

    public function calculateFieldsPoints($board) {
        $points = 0;
        $visited = []; // Array to keep track of visited yellow tokens

        foreach ($board as $hex) {
            // Consider only the top token of the hex
            $topToken = $hex['topToken'];

            if ($topToken && $topToken->type_arg === YELLOW && !isset($visited[$topToken->id])) {
                // If the top token is yellow and not visited yet, explore its zone
                $yellowHexesExplored = $this->exploreZone($board, $hex, $visited);

                // After exploring the zone, if it consists of several yellow hexes, increment points by 5
                if ($yellowHexesExplored > 1) {
                    $points += 5;
                }
            }
        }

        return $points;
    }

    // Function to recursively explore the zone of neighboring yellow tokens
    private function exploreZone($board, $hex, &$visited) {
        // Initialize the count of yellow hexes explored in this zone
        $yellowHexesExplored = 0;

        // Mark the current hex as visited
        $visited[$hex['topToken']->id] = true;

        // Get neighbors of the current hex
        $neighbors = $this->getNeighbours($hex);

        foreach ($neighbors as $neighbor) {
            // Consider only the top token of the neighbor hex
            $neighborHex = $board[$this->getHexIndexInBoard($board, $neighbor["col"], $neighbor["row"])];
            //self::dump('*******************$neighborHex', $neighborHex);
            $neighborTopToken = $neighborHex['topToken'];

            if ($neighborTopToken && $neighborTopToken->type_arg === YELLOW && !isset($visited[$neighborTopToken->id])) {
                // If the neighboring token is yellow and not visited yet, explore its zone recursively
                $yellowHexesExplored += $this->exploreZone($board, $neighborHex, $visited);
            }
        }

        // Increment the count of yellow hexes explored in this zone by 1
        $yellowHexesExplored++;

        // Return the count of yellow hexes explored in this zone
        return $yellowHexesExplored;
    }

    private function getTopTokenAtHexFromBoard($board, $coords) {
        $hex = array_values(array_filter($board, fn ($h) => $this->hexesEquals($h, $coords["col"], $coords["row"])))[0];
        return $hex["tokens"] ? $hex["tokens"][0] : null;
    }

    private function isBuilding($tokensInHex) {
        $topToken = $tokensInHex ? $tokensInHex[0] : null;
        return $topToken && $topToken->type_arg == RED && count($tokensInHex) == 2;
    }

    public function calculateAnimalCardsPoints() {
        $total = 0;
        $cards = $this->getAnimalCardsToScore();
        foreach ($cards as $card) {
            $cubesCount = count($this->getCubesOnCard($card));
            if ($cubesCount < count($card->pointLocations)) {
                $cardScore = $card->pointLocations[$cubesCount + 1];
                $total += $cardScore;
            }
        }
        return $total;
    }
}
