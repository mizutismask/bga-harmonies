<?php

require_once(__DIR__ . '/animalCard.php');

trait AnimalCardDeckTrait {

    /**
     * Create animal cards.
     */
    public function createAnimalCards() {
        $animalCards = $this->getAnimalCardsToGenerate();
        $this->animalCards->createCards($animalCards, 'deck');
        $this->animalCards->shuffle('deck');
    }

    /**
     * Pick spirits cards for beginning choice.
     */
    public function pickInitialSpiritsCards() {
        $cardsNumber = $this->getInitialSpiritCardNumber();
        $players = $this->getPlayersIds();
        $possibleSpirits = array_filter($this->ANIMAL_CARDS[1], fn($c) => $c->isSpirit === true);
        $possibleTypes = array_keys($possibleSpirits);
        foreach ($players as $playerId) {
            for ($c = 0; $c < $cardsNumber; $c++) {
                $cards  = $this->getCardsOfTypeArgAmongSeveralFromLocation("animalCard", $possibleTypes, "deck");
                $i = bga_rand(0, count($cards) - 1);

                $card = array_values($cards)[$i];
                $this->animalCards->moveCard($card["id"], "spirit", $playerId);

                $this->notifyAllPlayers('materialMove', "", [
                    'type' => MATERIAL_TYPE_CARD,
                    'from' => MATERIAL_LOCATION_DECK,
                    'to' => MATERIAL_LOCATION_SPIRITS,
                    'toArg' => $playerId,
                    'material' => [$this->getAnimalCardFromDb($card)],
                ]);
            }
        }
        //discards all others
        $remaining  = $this->getCardsOfTypeArgAmongSeveralFromLocation("animalCard", $possibleTypes, "deck");
        $this->animalCards->moveCards(array_map(fn($c) => $c["id"], $remaining), "discard");
    }

    public function getSpiritCardsToChoose($playerId) {
        return $this->getAnimalCardsFromDb($this->animalCards->getCardsInLocation("spirit", $playerId));
    }

    public function refillAnimalCards() {
        $visibleCardsCount = intval($this->animalCards->countCardInLocation('river'));
        $expectedCardsCount = $this->getPlayerCount() === 1 ? VISIBLE_ANIMAL_CARDS_COUNT_SOLO : VISIBLE_ANIMAL_CARDS_COUNT;
        $missingSeveral = $expectedCardsCount - $visibleCardsCount > 1;
        if ($visibleCardsCount < $expectedCardsCount) {
            $spots = [];
            for ($i = $visibleCardsCount; $i < $expectedCardsCount; $i++) {
                $newCard = $this->getAnimalCardFromDb($this->animalCards->pickCardForLocation('deck', 'river', $missingSeveral ? $i : self::getGameStateValue(EMPTIED_RIVER_SLOT)));
                $spots[] = $newCard;

                $this->notifyAllPlayers('materialMove', "", [
                    'type' => MATERIAL_TYPE_CARD,
                    'from' => MATERIAL_LOCATION_DECK,
                    'to' => MATERIAL_LOCATION_RIVER,
                    'material' => [$newCard],
                ]);
            }
        }
    }

    /**
     * Get cards in player hand.
     */
    public function getPlayerAnimalCards(int $playerId) {
        $cards = $this->getAnimalCardsFromDb($this->animalCards->getCardsInLocation("board" . $playerId));
        return $cards;
    }

    /**
     * get remaining cards in deck.
     */
    public function getRemainingAnimalCardsInDeck() {
        $remaining = intval($this->animalCards->countCardInLocation('deck'));

        if ($remaining == 0) {
            $remaining = intval($this->animalCards->countCardInLocation('discard'));
        }

        return $remaining;
    }

    public function moveAnimalCardToPlayerBoard(int $cardId) {
        $playerId = $this->getMostlyActivePlayerId();
        $card = $this->getAnimalCardFromDb($this->animalCards->getCard($cardId));
        $isSpirit = $card->isSpirit;
        $location = "board" . $playerId;
        $spot = $this->getFirstEmptySlot($playerId);
        $this->animalCards->moveCard($cardId, $location, $spot);
        $card = $this->getAnimalCardFromDb($this->animalCards->getCard($cardId));
        self::incStat(1, "game_animal_cards_taken", $playerId);

        $this->notifyAllPlayers('materialMove', clienttranslate('${player_name} takes ${cardType} card to his spot ${spot}'), [
            'playerId' => $playerId,
            'player_name' => $this->getPlayerName($playerId),
            'type' => MATERIAL_TYPE_CARD,
            'from' => $isSpirit ? MATERIAL_LOCATION_SPIRITS : MATERIAL_LOCATION_RIVER,
            'to' => MATERIAL_LOCATION_HAND,
            'toArg' => $playerId,
            'material' => [$card],
            'spot' => $spot + 1,
            'cardType' => $card->isSpirit ? clienttranslate('a spirit') : clienttranslate('an animal'),
            'i18n' => ['cardType'],
        ]);
        $this->fillAnimalCard($card);
    }

    public function discardAndReplaceAnimalCard(int $cardId) {
        $playerId = $this->getMostlyActivePlayerId();
        $card = $this->getAnimalCardFromDb($this->animalCards->getCard($cardId));
        $this->animalCards->playCard($cardId);

        $this->notifyAllPlayers('materialMove', clienttranslate('${player_name} discards a card from the river'), [
            'playerId' => $playerId,
            'player_name' => $this->getPlayerName($playerId),
            'type' => MATERIAL_TYPE_CARD,
            'from' => MATERIAL_LOCATION_RIVER,
            'to' => MATERIAL_LOCATION_DISCARD,
            'material' => [$card],
        ]);

        $this->refillAnimalCards();
    }

    private function getFirstEmptySlot($playerId) {
        $i = 0;
        $full = true;
        $location = "board" . $playerId;
        while ($i < 4 && $full) {
            $full = intval($this->animalCards->countCardInLocation($location, $i)) > 0;
            if ($full) $i++;
        }
        return $i;
    }

    public function moveAnimalCardToFinishedCards(int $cardId) {
        $playerId = $this->getMostlyActivePlayerId();
        $this->animalCards->moveCard($cardId, "done" . $playerId);

        $card = $this->getAnimalCardFromDb($this->animalCards->getCard($cardId));
        self::incStat(1, "game_animal_cards_finished", $playerId);

        $this->notifyAllPlayers('materialMove', clienttranslate('${player_name} finishes an animal card'), [
            'playerId' => $playerId,
            'player_name' => $this->getPlayerName($playerId),
            'type' => MATERIAL_TYPE_CARD,
            'from' => MATERIAL_LOCATION_HAND,
            'to' => MATERIAL_LOCATION_DONE,
            'toArg' => $playerId,
            'material' => [$card],
        ]);
    }

    public function getAnimalCardsToScore($playerId) {
        $pending = $this->getAnimalCardsOnPlayerBoard($playerId);
        $done = $this->getAnimalCardsDone($playerId);
        return array_merge($pending, $done);
    }

    public function getAnimalCardsOnPlayerBoard($playerId) {
        return $this->getAnimalCardsFromDb($this->animalCards->getCardsInLocation("board" . $playerId));
    }

    public function getAnimalCardsDone($playerId) {
        return $this->getAnimalCardsFromDb($this->animalCards->getCardsInLocation("done" . $playerId));
    }

    public function getAnimalCardsInRiver() {
        return $this->getAnimalCardsFromDb($this->animalCards->getCardsInLocation("river"));
    }
}
