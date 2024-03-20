<?php

trait ActionTrait {

    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    //////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in yourgamename.action.php)
    */
    /*public function chooseAdditionalDestinations(int $keptDestinationsId, int $discardedDestinationId) {
        self::checkAction('chooseAdditionalDestinations');

        $playerId = intval(self::getActivePlayerId());

        $this->keepAdditionalDestinationCards($playerId, $keptDestinationsId, $discardedDestinationId);

        if ($keptDestinationsId)
            self::incStat(1, STAT_KEPT_ADDITIONAL_DESTINATION_CARDS, $playerId);

        $this->gamestate->nextState('continue');
    }*/

    function pass() {
        self::checkAction('pass');

        $args = $this->argChooseAction();

        if (!$args['canPass']) {
            throw new BgaUserException("You cannot pass");
        }

        $this->gamestate->nextState('nextPlayer');
    }

    function takeTokens($holeNumber) {
        self::checkAction('takeTokens');

        $args = $this->argChooseAction();

        if (!$args['canTakeTokens']) {
            throw new BgaUserException(self::_("You already took colored tokens"));
        }

        $tokens = $this->getColoredTokensFromDb($this->coloredTokens->getCardsInLocation('centralBoard', $holeNumber));
        self::setGameStateValue(EMPTIED_HOLE, $holeNumber);
        $this->setGlobalVariable(TOKENS_IN_HOLE, $tokens);

        $this->notifyAllPlayers('holeEmptied',  clienttranslate('${player_name} takes tokens'), [
            'player_name' => $this->getPlayerName($this->getMostlyActivePlayerId()),
            'hole' => $holeNumber,
        ]);

        $this->gamestate->nextState('continue');
    }

    function takeAnimalCard($cardId) {
        self::checkAction('takeAnimalCard');

        $args = $this->argChooseAction();

        if (!$args['canTakeAnimalCard']) {
            throw new BgaUserException(self::_("You can’t take an animal card, you already did it on this turn or don’t have any space left"));
        }
        $card = $this->getAnimalCardFromDb($this->animalCards->getCard($cardId));
        if ($card->location != "river") {
            throw new BgaUserException(self::_("This card is not available in the river"));
        }
        self::setGameStateValue(EMPTIED_RIVER_SLOT,$card->location_arg);
        $this->moveAnimalCardToPlayerBoard($cardId);
        $this->gamestate->nextState('continue');
    }

    function placeAnimalCube($fromCardId, $toHexId) {
        self::checkAction('placeAnimal');

        $args = $this->argChooseAction();

        if (!$args['canPlaceAnimalCube']) {
           throw new BgaUserException(self::_("You can’t place an animal cube"));
        }
        $card = $this->getAnimalCardFromDb($this->animalCards->getCard($fromCardId));
        if (!$this->startsWith($card->location, "board")) {
            throw new BgaUserException(self::_("This card is not on your board"));
        }
        if(!isset($args["placeAnimalCubeArgs"][$fromCardId]) || !in_array($toHexId, $args["placeAnimalCubeArgs"][$fromCardId])){
            throw new BgaUserException(self::_("You have to respect the pattern of the card"));
        }

        $cube = $this->getLastCubeOnCard($fromCardId);
        self::dump('*******************getLastCubeOnCard', $cube);
        if ($cube) {
            $this->moveCubeToHex($cube, $toHexId, $fromCardId);
        } else {
            $this->moveAnimalCardToFinishedCards();
        }
        $this->gamestate->nextState('continue');
    }

    function placeColoredToken($tokenId, $toHexId) {
        self::checkAction('placeToken');

        $args = $this->argChooseAction();

        if (!$args['canPlaceToken']) {
            throw new BgaUserException(self::_("You can’t place a colored token"));
        }
        $tokenChosen = $this->getColoredTokensChosen();
        if (!$this->array_some($tokenChosen, fn ($tok) => $tok->id == $tokenId)) {
            throw new BgaUserException(self::_("You are not allowed to place this colored token"));
        }
        $possibleHexes = $this->getPossibleHexesForColoredToken($tokenId, $this->getMostlyActivePlayerId());
        //self::dump('*******************possibleHexes', $possibleHexes);
        if (!in_array($toHexId, $possibleHexes)) {
            throw new BgaUserException(self::_("You are not allowed to place this color here, check the player help"));
        }

        $this->moveColoredTokenToBoard($tokenId, $toHexId);

        $this->gamestate->nextState('continue');
    }
}
