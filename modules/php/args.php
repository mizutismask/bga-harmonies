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

        $canTakeTokens =  $this->getGlobalVariable(TOKENS_IN_HOLE) === null;
        $canPass = !$canTakeTokens;
        return [
            'canTakeTokens' => $canTakeTokens,
            'canTakeAnimalCard' => boolval(self::getGameStateValue(TOOK_ANIMAL_CARD)) === false && intval($this->animalCards->countCardInLocation("board".$playerId)) < 4,
            'canPlaceAnimalCube' => false,
            'canPass' => $canPass,
        ];
    }
}
