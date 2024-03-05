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
     * Pick destination cards for beginning choice.
     */
    /* public function pickInitialDestinationCards(int $playerId) {
        $cardsNumber = $this->getInitialDestinationCardNumber();
        $cards = $this->pickDestinationCards($playerId, $cardsNumber);
        $this->keepInitialDestinationCards($playerId, $this->getDestinationIds($cards), $this->getInitialDestinationCardNumber());
        return $cards;
    }*/

    public function refillAnimalCards() {
        $visibleCardsCount = intval($this->animalCards->countCardInLocation('river'));
        if ($visibleCardsCount < VISIBLE_ANIMAL_CARDS_COUNT) {
            $spots = [];
            for ($i = $visibleCardsCount; $i < VISIBLE_ANIMAL_CARDS_COUNT; $i++) {
                $newCard = $this->getAnimalCardFromDb($this->animalCards->pickCardForLocation('deck', 'river', $i));
                $spots[] = $newCard;
            }
            $this->notifyAllPlayers('newCardOnRiver', "", [
                'cards' => $spots,
            ]);
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
        $cards = $this->getAnimalCardsFromDb($this->animalCards->getCardsInLocation("hand", $playerId));
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
