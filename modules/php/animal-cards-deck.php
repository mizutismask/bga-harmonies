<?php

require_once(__DIR__ . '/objects/animalCard.php');

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
        $possibleSpirits = array_filter($this->ANIMAL_CARDS[1], fn ($c) => $c->isSpirit === true);
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
        $this->animalCards->moveCards(array_map(fn ($c) => $c["id"], $remaining), "discard");
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
     * Pick destination cards for pick destination action.
     */
    /* public function pickAdditionalDestinationCards(int $playerId) {
        return $this->pickDestinationCards($playerId, $this->getAdditionalDestinationCardNumber());
    }
*/
    /**
     * Select kept destination card for pick destination action. 
     * Unused destination cards are discarded.
     */
    /*  public function keepAdditionalDestinationCards(int $playerId, int $keptDestinationsId, int $discardedDestinationId) {
        $this->keepDestinationCards($playerId, $keptDestinationsId, $discardedDestinationId);
    }*/

    /**
     * Get destination picked cards (cards player can choose).
     */
    /*public function getPickedDestinationCards(int $playerId) {
        $cards = $this->getDestinationsFromDb($this->animalCards->getCardsInLocation("pick$playerId"));
        return $cards;
    }
*/
    /**
     * Get destination cards in player hand.
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

    /**
     * place a number of animalCards cards to pick$playerId.
     */
    /* private function pickDestinationCards($playerId, int $number) {
        $cards = $this->getDestinationsFromDb($this->animalCards->pickCardsForLocation($number, 'deck', "pick$playerId"));
        return $cards;
    }*/

    /**
     * move selected card to player hand, discard other selected card from the hand and empty pick$playerId.
     */
    /*  private function keepDestinationCards(int $playerId, int $keptDestinationsId, int $discardedDestinationId) {
        if ($keptDestinationsId xor $discardedDestinationId) {
            throw new BgaUserException("You must discard a destination to take another one.");
        }
        $traded = $keptDestinationsId && $discardedDestinationId;
        if ($traded) {
            if (
                $this->getUniqueIntValueFromDB("SELECT count(*) FROM destination WHERE `card_location` = 'pick$playerId' AND `card_id` = $keptDestinationsId") == 0
                || $this->getUniqueIntValueFromDB("SELECT count(*) FROM destination WHERE `card_location` = 'hand' AND `card_location_arg` = '$playerId' AND `card_id` = $discardedDestinationId") == 0
            ) {
                throw new BgaUserException("Selected cards are not available.");
            }
            $this->animalCards->moveCard($keptDestinationsId, 'hand', $playerId);
            $this->animalCards->moveCard($discardedDestinationId, 'discard');

            $remainingCardsInPick = intval($this->animalCards->countCardInLocation("pick$playerId"));
            if ($remainingCardsInPick > 0) {
                // we discard remaining cards in pick
                $this->animalCards->moveAllCardsInLocationKeepOrder("pick$playerId", 'discard');
            }
        }
        $this->notifyAllPlayers('destinationsPicked', clienttranslate('${player_name} trades ${count} destination'), [
            'playerId' => $playerId,
            'player_name' => $this->getPlayerName($playerId),
            'count' => intval($traded),
            'number' => 0, //1-1 or 0-0
            'remainingDestinationsInDeck' => $this->getRemainingAnimalCardsInDeck(),
            '_private' => [
                $playerId => [
                    'animalCards' => $this->getDestinationsFromDb([$this->animalCards->getCard($keptDestinationsId)]),
                    'discardedDestination' => $this->getAnimalCardFromDb($this->animalCards->getCard($discardedDestinationId)),
                ],
            ],
        ]);
    }*/

    /**
     * Move selected cards to player hand.
     */
    /* private function keepInitialDestinationCards(int $playerId, array $ids) {
        $this->animalCards->moveCards($ids, 'hand', $playerId);
        $this->notifyAllPlayers('destinationsPicked', clienttranslate('${player_name} keeps ${count} animalCards'), [
            'playerId' => $playerId,
            'player_name' => $this->getPlayerName($playerId),
            'count' => count($ids),
            'number' => count($ids),
            'remainingDestinationsInDeck' => $this->getRemainingAnimalCardsInDeck(),
            '_private' => [
                $playerId => [
                    'animalCards' => $this->getDestinationsFromDb($this->animalCards->getCards($ids)),
                ],
            ],
        ]);
    }*/
}
