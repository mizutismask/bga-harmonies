<?php

trait ActionTrait {

    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    //////////// 

    function pass() {
        self::checkAction('pass');

        $args = $this->argChooseAction();

        if (!$args['canPass']) {
            throw new BgaUserException("You cannot pass");
        }

        $this->goToDiscardOrNextPlayer();
    }

    function declineDiscard() {
        self::checkAction('declineDiscard');
        $this->gamestate->nextState('nextPlayer');
    }

    function takeTokens($playerId, $holeNumber, $zombie = false) {
        self::checkAction('takeTokens');

        if (!$zombie) {
            $args = $this->argChooseAction();
            if (!$args['canTakeTokens']) {
                throw new BgaUserException(self::_("You already took colored tokens"));
            }
        }

        $tokens = $this->getColoredTokensFromDb($this->coloredTokens->getCardsInLocation('centralBoard', $holeNumber));
        self::setGameStateValue(EMPTIED_HOLE, $holeNumber);
        $this->setGlobalVariable(TOKENS_IN_HOLE, $tokens);

        $this->notifyAllPlayers('holeEmptied',  clienttranslate('${player_name} takes ${tokens}'), [
            'player_name' => $this->getPlayerName($playerId),
            'hole' => $holeNumber,
            'tokens' => $tokens,
        ]);

        if (!$zombie) {
            $this->continueOrEndTurn();
        }
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
        self::setGameStateValue(EMPTIED_RIVER_SLOT, $card->location_arg);
        self::setGameStateValue(TOOK_ANIMAL_CARD, 1);
        $this->moveAnimalCardToPlayerBoard($cardId);
        $this->continueOrEndTurn();
    }

    function discardFromRiver($cardId) {
        self::checkAction('discardFromRiver');

        $card = $this->getAnimalCardFromDb($this->animalCards->getCard($cardId));
        if ($card->location != "river") {
            throw new BgaUserException(self::_("This card is not available in the river"));
        }
        self::setGameStateValue(EMPTIED_RIVER_SLOT, $card->location_arg);
        self::setGameStateValue(TOOK_ANIMAL_CARD, 1); //to prevent discarding again
        $this->discardAndReplaceAnimalCard($cardId);
        $this->gamestate->nextState('nextPlayer');
    }

    function chooseSpirit($cardId) {
        self::checkAction('chooseSpirit');
        $playerId = intval(self::getActivePlayerId());

        $possibleCards = $this->getSpiritCardsToChoose($playerId);
        if (!$this->isSpiritCardsOn() || count($possibleCards) == 0) {
            throw new BgaUserException(self::_("You can’t take a spirit card now"));
        }

        $card = $this->getAnimalCardFromDb($this->animalCards->getCard($cardId));
        if ($card->location != "spirit" || $card->location_arg != $playerId) {
            throw new BgaUserException(self::_("This card is not available to choose"));
        }

        $this->moveAnimalCardToPlayerBoard($cardId);

        $toDiscard = $this->getSpiritCardsToChoose($playerId);
        $this->systemAssertTrue("There is no second spirit to discard", count($toDiscard) === 1);
        $this->animalCards->playCard(array_pop($toDiscard)->id);

        $this->continueOrEndTurn();
    }


    function placeAnimalCube($fromCardId, $toHexId) {
        self::checkAction('placeAnimal');
        $playerId = intval(self::getActivePlayerId());

        $args = $this->argChooseAction();

        if (!$args['canPlaceAnimalCube']) {
            throw new BgaUserException(self::_("You can’t place an animal cube"));
        }
        $card = $this->getAnimalCardFromDb($this->animalCards->getCard($fromCardId));
        if (!$this->startsWith($card->location, "board")) {
            throw new BgaUserException(self::_("This card is not on your board"));
        }
        if (!isset($args["placeAnimalCubeArgs"][$fromCardId]) || !in_array($toHexId, $args["placeAnimalCubeArgs"][$fromCardId])) {
            throw new BgaUserException(self::_("You have to respect the pattern of the card"));
        }

        self::incStat(1, "game_animal_cubes", $playerId);

        //self::dump('*******************getLastCubeOnCard', $cube);
        $this->moveCubeToHex($this->getLastCubeOnCard($fromCardId), $toHexId, $fromCardId);
        $cube = $this->getLastCubeOnCard($fromCardId);
        if (!$cube) {
            $this->moveAnimalCardToFinishedCards($fromCardId);
        }
        $this->continueOrEndTurn();
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
            throw new BgaUserException(self::_("You are not allowed to place this color here, check the rules below in the section How to play"));
        }

        $this->moveColoredTokenToBoard($tokenId, $toHexId);

        $this->continueOrEndTurn();
    }

    function continueOrEndTurn() {
        $this->toggleResetTurn(true);
        $this->gamestate->nextState('continue');
    }

    function goToDiscardOrNextPlayer() {
        if ($this->getPlayerCount() === 1 && intval(self::getGameStateValue(TOOK_ANIMAL_CARD)) == 0 && !$this->hasReachedEndOfGameRequirements($this->getMostlyActivePlayerId())) {
            $this->gamestate->nextState('discardFromRiver');
        } else {
            $this->gamestate->nextState('nextPlayer');
        }
    }

    /** Undo all the player turn actions. */
    function resetPlayerTurn() {
        $possible = $this->getGlobalVariable(CAN_RESET_TURN);
        if (!$possible) {
            throw new BgaUserException(self::_("Undo is not available"));
        }
        $this->undoRestorePoint();
        $this->toggleResetTurn(false);
        $this->gamestate->reloadState();
    }
}