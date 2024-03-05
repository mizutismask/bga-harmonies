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

        $this->setGlobalVariable(TOKENS_IN_HOLE, $this->getColoredTokensFromDb($this->coloredTokens->getCardsInLocation('centralBoard', $holeNumber)));
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

        $this->moveAnimalCardToPlayerBoard($cardId);
        $this->gamestate->nextState('continue');
    }
}
