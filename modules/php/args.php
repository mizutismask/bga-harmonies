<?php

trait ArgsTrait {

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state arguments
    ////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    function argChooseAction() {
        $playerId = intval(self::getActivePlayerId());

        $takenTokens = $this->getColoredTokensChosen();
        $canTakeTokens = count($takenTokens) === 0;
        $canPlaceToken =  !$canTakeTokens && $this->array_some($takenTokens, fn ($tok) => $tok->done == false);
        $canPass = !$canTakeTokens && !$canPlaceToken;
        $animalCubeArgs = $this->argPlaceAnimalCube();
        $cubeHexes = $animalCubeArgs["hexByCardId"];
        $tokensPossiblePlaces = [];
        if ($canPlaceToken) {
            $toPlace = array_filter($takenTokens, fn ($tok) => $tok->done == false);
            foreach ($toPlace as $token) {
                $tokensPossiblePlaces[$token->id] = $this->getPossibleHexesForColoredToken($token->id, $this->getMostlyActivePlayerId());
            }
        }
        return [
            'canTakeTokens' => $canTakeTokens,
            'canPlaceToken' => $canPlaceToken,
            'canTakeAnimalCard' => boolval(self::getGameStateValue(TOOK_ANIMAL_CARD)) === false && intval($this->animalCards->countCardInLocation("board" . $playerId)) < 4,
            'canPlaceAnimalCube' => !empty($cubeHexes),
            'canChooseSpirit' => $this->isSpiritCardsOn() && count($this->getSpiritCardsToChoose($playerId)) > 0,
            'canPass' => $canPass,
            'canResetTurn' => $this->getGlobalVariable(CAN_RESET_TURN),
            'tokensOnCentralBoard' => $canTakeTokens ? $this->getColoredTokensOnCentralBoard() : [],
            'tokensToPlace' => $canPlaceToken ? array_values(array_filter($this->getColoredTokensChosen(), fn ($token) => $token->done == false)) : [],
            'placeAnimalCubeArgs' => $cubeHexes,
            'possibleCards' => $animalCubeArgs["possibleCards"],
            'possibleHexesByToken' => $tokensPossiblePlaces,
        ];
    }

    function argPlaceAnimalCube() {
        $playerId = intval(self::getActivePlayerId());
        $cards = $this->getPlayerAnimalCards($playerId);
        $board = $this->getBoard($playerId);
        $possible = [];
        $possibleCards = [];

        $existingCubesLocs = array_keys($this->getAnimalCubesOnPlayerBoard($playerId));
        //self::dump('*******************existingCubesLocs', $existingCubesLocs);
        foreach ($cards as $card) {
            $locations = $this->getPossibleLocationsForCubeInPattern($board, $card, true, $playerId);
            if ($locations) {
                //self::dump('*******************locations', $locations);
                $freeLocations = array_diff($locations, $existingCubesLocs);
                if ($freeLocations) {
                    $possible[$card->id] = array_values($freeLocations);
                    $possibleCards[] = $card;
                }
            }
        }
        return [
            'hexByCardId' => $possible,
            'possibleCards' => $possibleCards,
        ];
    }
}
