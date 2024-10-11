<?php

require_once(__DIR__ . '/objects/coloredToken.php');
const TABLE_COLORED_TOKEN = "coloredToken";
trait ColoredTokenDeckTrait {

    /**
     * Create token cards.
     */
    public function createTokens() {
        $tokens = $this->getColoredTokensToGenerate();
        $this->coloredTokens->createCards($tokens, 'deck');
        $this->coloredTokens->shuffle('deck');

        //add the constraint after the deck is shuffled, because unicity for the constraint is not done before (card_location=deck and card_location_arg=0)
        self::applyDbUpgradeToAllDB("ALTER TABLE DBPREFIX_coloredToken ADD CONSTRAINT UC_CellLevel UNIQUE (card_location, card_location_arg)");
    }

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
            $toDiscard = $this->getCardsFromLocationLike(TABLE_COLORED_TOKEN, "centralBoard_");
            $idsToDiscard = array_keys($toDiscard);
            foreach ($idsToDiscard as $id) {
                $this->coloredTokens->insertCardOnExtremePosition($id, "discard", true);
            }
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

    public function getTokenCountOnCell($hexId) {
        return $this->getUniqueIntValueFromDB("SELECT count(card_id) FROM coloredToken where `card_location` = '$hexId'");
    }

    public function moveColoredTokenToBoard($tokenId, $hexId) {
        $playerId = $this->getMostlyActivePlayerId();
        $zindex = $this->getTokenCountOnCell($hexId) + 1;
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


            /*self::dump('*******************existingTokens', $existingTokens);
            self::dump('*******************isColorAllowedOnTopOfOtherColor', $this->isColorAllowedOnTopOfOtherColor($token->type_arg, $existingTokens[0]->type_arg));
            self::dump('*******************isColorAllowedAtPosition', $this->isColorAllowedAtPosition($token->type_arg, count($existingTokens) + 1));
            self::dump('*******************alreadyHasCube', $alreadyHasCube);*/
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
    private function pickTokensForCentralBoard(int $count, int $holeNumber) {
        $cards = [];
        for ($i = 0; $i < $count; $i++) {
            $cards[] = $this->getColoredTokenFromDb($this->coloredTokens->pickCardForLocation("deck", "centralBoard_$holeNumber", $i + 1));
        }
        return $cards;
    }

    public function getColoredTokensOnCentralBoard() {
        $tokens = $this->getColoredTokensFromDb($this->getCardsFromLocationLike(TABLE_COLORED_TOKEN, 'centralBoard_'));
        $byHole = $this->getPlayerCount() === 1 ? array_fill_keys([1, 2, 3], []) : array_fill_keys([1, 2, 3, 4, 5], []);
        foreach ($tokens as $tok) {
            $byHole[$this->getPart($tok->location, -1)][] = $tok;
        }
        return $byHole;
    }

    public function getRemainingTokensInDeck() {
        return intval($this->coloredTokens->countCardInLocation("deck"));
    }
}
