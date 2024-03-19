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
    /*function argChooseAdditionalDestinations() {
        $playerId = intval(self::getActivePlayerId());

        $ANIMAL_CARDS = $this->getPickedDestinationCards($playerId);

        return [
            'minimum' => 3,
            '_private' => [          // Using "_private" keyword, all data inside this array will be made private
                'active' => [       // Using "active" keyword inside "_private", you select active player(s)
                    'ANIMAL_CARDS' => $ANIMAL_CARDS,   // will be send only to active player(s)
                ]
            ],
        ];
    }
*/

    function argChooseAction() {
        $playerId = intval(self::getActivePlayerId());

        $takenTokens = $this->getColoredTokensChosen();
        $canTakeTokens = count($takenTokens) === 0;
        $canPass = !$canTakeTokens;
        $canPlaceToken =  !$canTakeTokens && $this->array_some($takenTokens, fn ($tok) => $tok->done == false);
        $animalCubeArgs = $this->argPlaceAnimalCube()["hexByCardId"];
        return [
            'canTakeTokens' => $canTakeTokens,
            'canPlaceToken' => $canPlaceToken,
            'canTakeAnimalCard' => boolval(self::getGameStateValue(TOOK_ANIMAL_CARD)) === false && intval($this->animalCards->countCardInLocation("board" . $playerId)) < 4,
            'canPlaceAnimalCube' => !empty($animalCubeArgs),
            'canPass' => $canPass,
            'tokensOnCentralBoard' => $canTakeTokens ? $this->getColoredTokensOnCentralBoard() : [],
            'tokensToPlace' => $canPlaceToken ? array_values(array_filter($this->getColoredTokensChosen(), fn ($token) => $token->done == false)) : [],
            'placeAnimalCubeArgs' => $animalCubeArgs,
        ];
    }

    function argPlaceAnimalCube() {
        $playerId = intval(self::getActivePlayerId());
        $cards = $this->getPlayerAnimalCards($playerId);
        $board = $this->getBoard($playerId);
        $possible = [];

        foreach ($cards as $card) {
            $locations = $this->getPossibleLocationsForCubeInPattern($board, $card, true, $playerId);
            if ($locations) {
                $possible[$card->id] = $locations;
            }
        }
        return [
            'hexByCardId' => $possible,
        ];
    }
}
