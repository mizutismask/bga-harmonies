<?php

require_once(__DIR__ . '/objects/coloredToken.php');

trait ColoredTokenDeckTrait {

    /**
     * Create destination cards.
     */
    public function createTokens() {
        $tokens = $this->getColoredTokensToGenerate();
        $this->coloredTokens->createCards($tokens, 'deck');
        $this->coloredTokens->shuffle('deck');
    }

    /**
     * Pick destination cards for beginning choice.
     */
    /* public function (pickInitialSpiritsCards)(int $playerId) {
        $cardsNumber = $this->getInitialSpiritCardNumber();
        $cards = $this->pickDestinationCards($playerId, $cardsNumber);
        $this->keepInitialDestinationCards($playerId, $this->getDestinationIds($cards), $this->getInitialSpiritCardNumber());
        return $cards;
    }*/

    /**
     * Pick tokens to fill central board.
     */
    public function fillCentralBoard() {
        $holeCount = $this->getPlayerCount() === 1 ? 3 : 5;
        $tokenCount = 3;
        for ($i = 1; $i <= $holeCount; $i++) {
            $tokens = $this->pickTokensForCentralBoard($tokenCount, $i);
            $this->notifyAllPlayers('materialMove', "", [
                'type' => MATERIAL_TYPE_TOKEN,
                'from' => MATERIAL_LOCATION_DECK,
                'to' => MATERIAL_LOCATION_HOLE,
                'toArg' => $i,
                'material' => $tokens,
            ]);
        }
    }

    /**
     * Pick tokens to refill central board.
     */
    public function refillCentralBoard() {
        if ($this->getPlayerCount() === 1) {
            //discard all tokens before replenishing
            $toDiscard = $this->coloredTokens->getCardsInLocation("centralBoard");
            $this->coloredTokens->moveCards(array_keys($toDiscard), "discard");
            $this->fillCentralBoard();
        } else {
            $hole = intval(self::getGameStateValue(EMPTIED_HOLE));
            if ($hole) {
                $tokenCount = 3;
                $tokens = $this->pickTokensForCentralBoard($tokenCount, $hole);
                if (count($tokens) != $tokenCount) {
                    //end of game
                } else {
                    $this->notifyAllPlayers('materialMove', "", [
                        'type' => MATERIAL_TYPE_TOKEN,
                        'from' => MATERIAL_LOCATION_DECK,
                        'to' => MATERIAL_LOCATION_HOLE,
                        'toArg' => $hole,
                        'material' => $tokens,
                    ]);
                }
            }
        }
        $this->notifyAllPlayers('counter', "", [
            'counterName' => "remainingTokens",
            'counterValue' => $this->getDisplayedRemainingTokensInDeck(),
        ]);
    }

    public function moveColoredTokenToBoard($tokenId, $hexId) {
        $playerId = $this->getMostlyActivePlayerId();
        $zindex = count($this->getTokensAt($hexId, $playerId)) + 1;
        $this->coloredTokens->moveCard($tokenId, $hexId, $zindex);
        $this->updateChosenToken($tokenId, true);

        $this->notifyAllPlayers('materialMove', "", [
            'type' => MATERIAL_TYPE_TOKEN,
            'from' => MATERIAL_LOCATION_DECK,
            'to' => MATERIAL_LOCATION_HEX,
            'toArg' => $playerId,
            'material' => [$this->getColoredTokenFromDb($this->coloredTokens->getCard($tokenId))],
        ]);

        $this->notifyAllPlayers('counter', "", [
            //'counterName' => "empty-hexes-counter-${playerId}",
            'counterName' => "empty-hexes",
            'counterValue' => $this->getEmptyHexesCount($playerId),
            'playerId' => $playerId,
        ]);
    }

    /* Called by zombie */
    public function discardChosenTokens() {
        $tokens = $this->getColoredTokensChosen();
        foreach ($tokens as $token) {
            $this->coloredTokens->moveCard($token->id, "discard");
            $this->updateChosenToken($token->id, true);
        }
    }

    public function getTokensAt($hexId, $playerId) {
        $location = $hexId;
        $tokens = $this->getColoredTokensFromDb($this->coloredTokens->getCardsInLocation($location, null, "location_arg desc"));
        return $tokens;
    }

    public function getTopTokenAt($hexId) {
        $location = $hexId;
        $token = $this->getColoredTokenFromDb($this->coloredTokens->getCardOnTop($location));
        return $token;
    }

    public function getTokensForCompleteBoardByHex($playerId) {
        $sql = "SELECT card_id id, card_type type, card_type_arg type_arg, card_location location, card_location_arg location_arg FROM coloredToken where card_location like 'cell_$playerId%' order by card_location_arg desc";
        $tokens = $this->getColoredTokensFromDb(self::getCollectionFromDb($sql));
        $byCell = [];
        foreach (array_values($tokens) as $token) {
            if (!isset($byCell[$token->location])) {
                $byCell[$token->location] = [];
            }
            $byCell[$token->location][] = $token;
        }
        return $byCell;
    }

    public function getPossibleHexesForColoredToken(string $tokenId, $playerId) {
        $board = $this->getBoard($playerId);
        $hexes = [];
        $token = $this->getColoredTokenFromDb($this->coloredTokens->getCard($tokenId));
        $existingCubesLocs = array_keys($this->getAnimalCubesOnPlayerBoard($playerId));
        foreach ($board as $hex) {
            $existingTokens = $hex["tokens"];
            //self::dump('*******************hex', $hex);
            $cellName = $this->getCellName($hex, $playerId);
            $alreadyHasCube = in_array($cellName, $existingCubesLocs);

            if (
                !$existingTokens
                || count($existingTokens) < 3
                && $this->isColorAllowedOnTopOfOtherColor($token->type_arg, $existingTokens[0]->type_arg)
                && $this->isColorAllowedAtPosition($token->type_arg, count($existingTokens) + 1)
                && !$alreadyHasCube
            ) {
                $hexes[] = $hex;
            }
        }
        return $this->convertHexesCoordsToName($hexes, $playerId);
    }

    public function convertHexesCoordsToName($hexes, $playerId) {
        $hexesNames = [];
        foreach ($hexes as $hex) {
            $hexesNames[] = $this->getCellName($hex, $playerId);
        }
        return $hexesNames;
    }

    public function isColorAllowedOnTopOfOtherColor($topColor, $bottomColor) {
        $allowed = true;
        if ($topColor === BLUE || $topColor === YELLOW) {
            $allowed = false;
        } else if ($bottomColor === BLUE || $bottomColor === YELLOW || $bottomColor === GREEN) {
            $allowed = false;
        } else if ($bottomColor === GRAY && $topColor !== GRAY && $topColor !== RED) {
            $allowed = false;
        } else if ($bottomColor === RED && $topColor !== $bottomColor) {
            $allowed = false;
        } else if ($topColor === GRAY && $topColor !== $bottomColor) {
            $allowed = false;
        }
        return $allowed;
    }

    public function isColorAllowedAtPosition($topColor, int $position) {
        $allowed = true;
        if ($topColor === RED && $position == 3) {
            $allowed = false;
        } else if ($topColor === BROWN && $position == 3) {
            $allowed = false;
        }
        return $allowed;
    }

    /* public function checkVisibleSharedCardsAreEnough() {
        $visibleCardsCount = intval($this->coloredTokens->countCardInLocation('shared'));
        if ($visibleCardsCount < NUMBER_OF_SHARED_DESTINATION_CARDS) {
            $spots = [];
            $citiesNames = [];
            for ($i = $visibleCardsCount; $i < NUMBER_OF_SHARED_DESTINATION_CARDS; $i++) {
                $newCard = $this->getColoredTokenFromDb($this->coloredTokens->pickCardForLocation('deck', 'shared', $i));
                $citiesNames[] = $this->CITIES[$newCard->to];
                $spots[] = $newCard;
            }
            $this->notifyAllPlayers('newSharedDestinationsOnTable', clienttranslate('New shared destination drawn: ${cities_names}'), [
                'sharedDestinations' => $spots,
                'cities_names' => implode(",", $citiesNames),
            ]);
        }
    }*/

    /**
     * Pick destination cards for pick destination action.
     */
    public function pickAdditionalDestinationCards(int $playerId) {
        return $this->pickDestinationCards($playerId, $this->getAdditionalDestinationCardNumber());
    }

    /**
     * Select kept destination card for pick destination action. 
     * Unused destination cards are discarded.
     */
    public function keepAdditionalDestinationCards(int $playerId, int $keptDestinationsId, int $discardedDestinationId) {
        $this->keepDestinationCards($playerId, $keptDestinationsId, $discardedDestinationId);
    }

    /**
     * Get destination picked cards (cards player can choose).
     */
    public function getPickedDestinationCards(int $playerId) {
        $cards = $this->getColoredTokensFromDb($this->coloredTokens->getCardsInLocation("pick$playerId"));
        return $cards;
    }

    /**
     * Get destination cards in player hand.
     */
    public function getPlayerDestinationCards(int $playerId) {
        $cards = $this->getColoredTokensFromDb($this->coloredTokens->getCardsInLocation("hand", $playerId));
        return $cards;
    }

    /**
     * Get remaining tokens in theory. Tokens picked are moved only when placed on player board.
     */
    public function getDisplayedRemainingTokensInDeck() {
        $remaining = $this->getRemainingTokensInDeck();
        return $remaining - count($this->getColoredTokensChosen());
    }

    /**
     * place a number of tokens cards to pick$playerId.
     */
    private function pickDestinationCards($playerId, int $number) {
        $cards = $this->getColoredTokensFromDb($this->coloredTokens->pickCardsForLocation($number, 'deck', "pick$playerId"));
        return $cards;
    }

    /**
     * place a number of tokens cards to pick$playerId.
     */
    private function pickTokensForCentralBoard(int $count, int $holeNumber) {
        $cards = $this->getColoredTokensFromDb($this->coloredTokens->pickCardsForLocation($count, "deck", 'centralBoard', $holeNumber));
        return $cards;
    }

    public function getColoredTokensOnCentralBoard() {
        $tokens = $this->getColoredTokensFromDb($this->coloredTokens->getCardsInLocation('centralBoard'));
        $byHole = $this->getPlayerCount() === 1 ? array_fill_keys([1, 2, 3], []) : array_fill_keys([1, 2, 3, 4, 5], []);
        foreach ($tokens as $tok) {
            $byHole[$tok->location_arg][] = $tok;
        }
        return $byHole;
    }

    public function getRemainingTokensInDeck() {
        return intval($this->coloredTokens->countCardInLocation("deck"));
    }

    /**
     * move selected card to player hand, discard other selected card from the hand and empty pick$playerId.
     */
    private function keepDestinationCards(int $playerId, int $keptDestinationsId, int $discardedDestinationId) {
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
            $this->coloredTokens->moveCard($keptDestinationsId, 'hand', $playerId);
            $this->coloredTokens->moveCard($discardedDestinationId, 'discard');

            $remainingCardsInPick = intval($this->coloredTokens->countCardInLocation("pick$playerId"));
            if ($remainingCardsInPick > 0) {
                // we discard remaining cards in pick
                $this->coloredTokens->moveAllCardsInLocationKeepOrder("pick$playerId", 'discard');
            }
        }
        $this->notifyAllPlayers('destinationsPicked', clienttranslate('${player_name} trades ${count} destination'), [
            'playerId' => $playerId,
            'player_name' => $this->getPlayerName($playerId),
            'count' => intval($traded),
            'number' => 0, //1-1 or 0-0
            'remainingDestinationsInDeck' => $this->getRemainingTokensInDeck(),
            '_private' => [
                $playerId => [
                    'tokens' => $this->getColoredTokensFromDb([$this->coloredTokens->getCard($keptDestinationsId)]),
                    'discardedDestination' => $this->getColoredTokenFromDb($this->coloredTokens->getCard($discardedDestinationId)),
                ],
            ],
        ]);
    }

    /**
     * Move selected cards to player hand.
     */
    private function keepInitialDestinationCards(int $playerId, array $ids) {
        $this->coloredTokens->moveCards($ids, 'hand', $playerId);
        $this->notifyAllPlayers('destinationsPicked', clienttranslate('${player_name} keeps ${count} tokens'), [
            'playerId' => $playerId,
            'player_name' => $this->getPlayerName($playerId),
            'count' => count($ids),
            'number' => count($ids),
            'remainingDestinationsInDeck' => $this->getRemainingTokensInDeck(),
            '_private' => [
                $playerId => [
                    'tokens' => $this->getColoredTokensFromDb($this->coloredTokens->getCards($ids)),
                ],
            ],
        ]);
    }
}
