<?php

require_once(__DIR__ . '/objects/animalCard.php');
trait ScoreTrait {
    static $treePoints = [1, 3, 7];
    static $riverPoints = [0, 2, 5, 8, 11, 15];
    public function calculateTreePoints($board) {
        $total = 0;
        foreach ($board as $hex) {
            $tokens = $hex["tokens"];
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
        while (count(array_unique($colors)) < $goal && $i < count($neighbours)) {
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
        //self::dump('*******************getZonesOfColor', $exploredZones);
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
            $blueZones = $this->getZonesOfColor($board, fn ($coloredToken) => $coloredToken !== null && $coloredToken->type_arg == BLUE);
            $maxDistance = 0;
            foreach ($blueZones as $zone) {
                //self::dump('*******************calculateWaterPoints for zone', $this->isBoardSideA());
                $path = $this->findLongestPathInBlueZone($zone);
                $distance = $path[2];
                if ($distance > $maxDistance) {
                    $maxDistance = $distance;
                }
                //echo "longest" . $path[0]["col"] . "_" . $path[0]["row"]  . " to " . $path[1]["col"] . "_" . $path[1]["row"] . " = " . $path[2];
            }
            $score = self::$riverPoints[min(6, $maxDistance) - 1];
            for ($i = 6; $i < $maxDistance; $i++) {
                $score += 4;
            }
            return $score;
        } else {
            return count($this->getZonesOfColor($board, fn ($coloredToken) => $coloredToken == null || $coloredToken->type_arg !== BLUE)) * 5;
        }
    }

    public function findLongestPathInBlueZone($blueZone) {
        $maxDistance = 0;
        $longestPath = [];

        // Parcourez chaque hexagone dans la zone bleue
        foreach ($blueZone as $hex1) {
            foreach ($blueZone as $hex2) {
                // Calculez la distance entre chaque paire d'hexagones
                $distance = $this->calculateDistanceBetweenHexagons($hex1, $hex2, $blueZone);
                if ($distance > $maxDistance) {
                    // Mettez à jour la plus grande distance et le chemin correspondant
                    $maxDistance = $distance;
                    $longestPath = [$hex1, $hex2, $maxDistance];
                }
            }
        }

        return $longestPath;
    }

    private function calculateDistanceBetweenHexagons($hex1, $hex2, $blueZone) {
        // Initialize the distance
        $distance = 0;

        // Perform a pathfinding algorithm to find the distance between the hexagones within the blue zone
        $path = $this->findShortestPathInBlueZone($hex1, $hex2, $blueZone);

        // If a valid path is found, calculate the distance
        if (!empty($path)) {
            $distance = count($path);
        }

        return $distance;
    }

    public function findShortestPathInBlueZone($start, $end, $blueZone) {
        $visited = [];
        $queue = new SplQueue();
        $previous = [];

        $queue->enqueue($start);
        $visited[$this->getTempHexId($start)] = true;

        while (!$queue->isEmpty()) {
            $current = $queue->dequeue();

            if ($current === $end) {
                break;
            }

            foreach ($this->getNeighbours($current) as $neighbor) {
                if ($this->isHexInZone($neighbor, $blueZone) && !isset($visited[$this->getTempHexId($neighbor)])) {
                    $queue->enqueue($neighbor);
                    $visited[$this->getTempHexId($neighbor)] = true;
                    $previous[$this->getTempHexId($neighbor)] = $current;
                }
            }
        }

        $path = [];
        $current = $end;
        while ($current !== null) {
            array_unshift($path, $current);
            $current = $previous[$this->getTempHexId($current)] ?? null;
        }

        return $path;
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

    public function calculateAnimalCardsPoints($playerId, $board) {
        $cards = $this->getAnimalCardsToScore($playerId);
        $normalCards = array_filter($cards, fn ($c) => !$c->isSpirit);
        $points = [];
        foreach ($normalCards as $card) {
            $cardScore = 0;
            $cubesCount = count($this->getCubesOnCard($card->id));
            if ($cubesCount < count($card->pointLocations)) {
                $cardScore = $card->pointLocations[$cubesCount];
            }
            $points[] = $cardScore;
        }
        $spiritPoints = $this->calculateSpiritCardsPoints($playerId, $board);
        return array_merge($spiritPoints, $points);
    }

    public function calculateSpiritCardsPoints($playerId, $board) {
        $points = [];
        if ($this->isSpiritCardsOn()) {
            $cards = $this->getAnimalCardsToScore($playerId);
            $spiritCards = array_filter($cards, fn ($c) => $c->isSpirit);
            foreach ($spiritCards as $card) {
                $cardScore = 0;
                $cubesCount = count($this->getCubesOnCard($card->id));
                if ($cubesCount < count($card->pointLocations)) {
                    $cardScore = $this->calculatePointsForSpiritCard($playerId, $card, $board);
                }
                $points[] = $cardScore;
            }
        }
        return $points;
    }

    public function calculatePointsForSpiritCard($playerId, $card, $board) {
        $points = 0;
        switch ($card->type_arg) {
            case 33:
            case 34:
                $zones = $this->getZonesOfColor($board, fn ($coloredToken) => $coloredToken !== null && $coloredToken->type_arg == YELLOW);
                $pointsPerZone = array_map(fn ($count) => $this->getPointsAccordingToZoneLength($card->type_arg, $count), array_map(fn ($z) => count($z), $zones));
                $points = array_sum($pointsPerZone);
                break;
            case 37:
            case 38:
                $zones = $this->getZonesOfColor($board, fn ($coloredToken) => $coloredToken !== null && $coloredToken->type_arg == RED);
                $pointsPerZone = array_map(fn ($count) => $this->getPointsAccordingToZoneLength($card->type_arg, $count), array_map(fn ($z) => count($z), $zones));
                $points = array_sum($pointsPerZone);
                break;
            case 41:
                $zones = $this->getZonesOfColor($board, fn ($coloredToken) => $coloredToken !== null && $coloredToken->type_arg == BLUE);
                $pointsPerZone = array_map(fn ($count) => $this->getPointsAccordingToZoneLength($card->type_arg, $count), array_map(fn ($z) => count($z), $zones));
                $points = array_sum($pointsPerZone);
                break;

            case 35:
                $score1 = $this->getPointsForPattern($card->type_arg, $board, [GREEN, BROWN]);
                $score2 = $this->getPointsForPattern($card->type_arg, $board, [GREEN, BROWN, BROWN]);
                $points = $score1 + $score2;
                break;
            case 36:
                $score1 = $this->getPointsForPattern($card->type_arg, $board, [GREEN]);
                $score2 = $this->getPointsForPattern($card->type_arg, $board, [GREEN, BROWN]);
                $score3 = $this->getPointsForPattern($card->type_arg, $board, [GREEN, BROWN, BROWN]);
                $points = $score1 + $score2 + $score3;
                break;
            case 39:
                $score1 = $this->getPointsForPattern($card->type_arg, $board, [GRAY, GRAY]);
                $score2 = $this->getPointsForPattern($card->type_arg, $board, [GRAY, GRAY, GRAY]);
                $points = $score1 + $score2;
                break;
            case 40:
                $score1 = $this->getPointsForPattern($card->type_arg, $board, [GRAY]);
                $score2 = $this->getPointsForPattern($card->type_arg, $board, [GRAY, GRAY]);
                $score3 = $this->getPointsForPattern($card->type_arg, $board, [GRAY, GRAY, GRAY]);
                $points = $score1 + $score2 + $score3;
                break;
            case 42:
                $points = $this->getPointsForPattern($card->type_arg, $board, [BLUE]);
                break;

            default:
                throw new BgaSystemException("This spirit card can not be calculated : " . $card->type_arg);
        }
        return $points;
    }

    private function getPointsAccordingToZoneLength($cardType, $length) {
        switch ($cardType) {
            case 33:
                return $length >= 3 ? 10 : 2;
            case 34:
                return 5;
            case 37:
                return 4;
            case 38:
                return $length >= 2 ? 6 : 0;
            case 41:
                return $length >= 2 ? 7 : 0;

            default:
                throw new BgaSystemException("This spirit card has not points defined : " . $cardType);
        }
    }
    private function getPointsForPattern($cardType, $board, $pattern) {
        $total = 0;
        foreach ($board as $hex) {
            $expected = $this->areExpectedTokensInHex($board, $hex["col"], $hex["row"], $pattern);
            if ($expected) {
                $total += $this->getPointsAccordingPatternHeight($cardType, count($pattern));
            }
        }
        return $total;
    }

    private function getPointsAccordingPatternHeight($cardType, $height) {
        switch ($cardType) {
            case 35:
                return $height >= 2 ? 4 : 0;
            case 36:
                return $height < 3 ? 3 : 1;
            case 39:
                return  $height >= 2 ? 4 : 0;
            case 40:
                return $height < 3 ? 3 : 1;
            case 42:
                return $height == 1 ? 2 : 0;

            default:
                throw new BgaSystemException("This spirit card has not points defined for height: " . $cardType);
        }
    }

    public function convertScoreToSuns($score): int {
        if ($this->isValueInRange($score, 0, 39)) return 0;
        if ($this->isValueInRange($score, 40, 69)) return 1;
        if ($this->isValueInRange($score, 70, 89)) return 2;
        if ($this->isValueInRange($score, 90, 109)) return 3;
        if ($this->isValueInRange($score, 110, 129)) return 4;
        if ($this->isValueInRange($score, 130, 139)) return 5;
        if ($this->isValueInRange($score, 140, 149)) return 6;
        if ($this->isValueInRange($score, 150, 159)) return 7;
        return 8;
    }
}
