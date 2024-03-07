<?php

require_once(__DIR__ . '/objects/animalCard.php');
trait ScoreTrait {
    static $treePoints = [1, 3, 7];
    public function calculateTreePoints($board) {
        $total = 0;
        foreach ($board as $hex) {
            $tokens = $this->getTokensAt($hex);
            $topToken = array_pop($tokens);
            if ($topToken && $topToken->type_arg == GREEN) {
                $total += self::$treePoints[count($tokens)];
            }
        }
        return $total;
    }

    public function calculateBuildingPoints($board) {
        $total = 0;
        foreach ($board as $hex) {
            $tokens = $this->getTokensAt($hex);
            $topToken = array_pop($tokens);
            if ($topToken && $topToken->type_arg == RED) {
                //todo check if surrounded by 3 different colors
                $total += 5;
            }
        }
        return $total;
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
