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
        return array_sum(array_map(fn ($zone) => count($zone) > 1 ? 5 : 0, $this->getZonesOfColor($board, fn ($coloredToken) => $coloredToken != null && $coloredToken->type_arg === YELLOW)));
    }

    public function getZonesOfColor($board, $zonePredicate) {
        $visited = []; // Array to keep track of visited tokens
        $exploredZones = []; // Array to store hexes of the explored zones

        foreach ($board as $hex) {
            // Consider only the top token of the hex
            $topToken = $hex['topToken'];

            if ($zonePredicate($topToken) && !isset($visited[$this->getTempHexId($hex)])) {
                // If the top token matches the specified color and is not visited yet, explore its zone
                $exploredZone = []; // Array to store hexes of the explored zone
                $this->exploreZone($board, $hex, $visited, $exploredZone, $zonePredicate);

                // Add the explored zone to the list of explored zones
                $exploredZones[] = $exploredZone;
            }
        }
        self::dump('*******************getZonesOfColor', $exploredZones);
        return $exploredZones;
    }

    private function getTempHexId(array $hex): string {
        return $hex['col'] . "_" . $hex['row'];
    }

    // Function to recursively explore the zone of neighboring tokens of specified color
    private function exploreZone($board, $hex, &$visited, &$exploredZone, $zonePredicate) {
        // Mark the current hex as visited
        $visited[$this->getTempHexId($hex)] = true;
        $exploredZone[] = $hex; // Add the current hex to the explored zone

        // Get neighbors of the current hex
        $neighbors = $this->getNeighbours($hex);

        foreach ($neighbors as $neighbor) {
            // Consider only the top token of the neighbor hex
            $neighborHex = $board[$this->getHexIndexInBoard($board, $neighbor["col"], $neighbor["row"])];
            //self::dump('*******************$neighborHex', $neighborHex);
            $neighborTopToken = $neighborHex['topToken'];

            if ($zonePredicate($neighborTopToken) && !isset($visited[$this->getTempHexId($neighborHex)])) {
                // If the neighboring token matches the specified color and is not visited yet, explore its zone recursively
                $this->exploreZone($board, $neighborHex, $visited, $exploredZone, $zonePredicate);
            }
        }
    }

    public function calculateMountainsPoints($board) {
        return array_sum(array_map(fn ($zone) => count($zone) > 1 ? $this->countPointsFromMoutainZone($zone) : 0, $this->getZonesOfColor($board, fn ($coloredToken) => $coloredToken != null && $coloredToken->type_arg === GRAY)));
    }

    public function calculateWaterPoints($board) {
        if ($this->isBoardSideA()) {
        } else {
            return count($this->getZonesOfColor($board, fn ($coloredToken) => $coloredToken == null || $coloredToken->type_arg !== BLUE)) * 5;
        }
    }

    private function countPointsFromMoutainZone($zone) {
        $mountainsPoints = [1, 3, 7];
        return array_sum(array_map(fn ($hex) => $mountainsPoints[count($hex["tokens"]) - 1], $zone));
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
