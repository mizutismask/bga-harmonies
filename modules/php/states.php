<?php

trait StateTrait {

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state actions
    ////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    function stDealInitialSetup() {
        $playersIds = $this->getPlayersIds();
        $this->fillCentralBoard();
        if ($this->isSpiritCardsOn())
            $this->pickInitialSpiritsCards();

        $this->refillAnimalCards();
        $this->gamestate->nextState('');
    }

    function hasReachedEndOfGameRequirements($playerId): bool {
        $playersIds = $this->getPlayersIds();

        $end = $this->has2OrLessEmptyHexes($playerId) || $this->getRemainingTokensInDeck() < 3;
        if ($end && intval(self::getGameStateValue(LAST_TURN) == 0)) {
            self::setGameStateValue(LAST_TURN, $this->getLastPlayer()); //we play until the last player to finish the round
            if (!$this->isLastPlayer($playerId)) {
                self::notifyWithName('lastTurn', clienttranslate('${player_name} has two or less empty spaces, finishing round !'), []);
            }
        }
        return $end;
    }

    function has2OrLessEmptyHexes($playerId): bool {
        $hexes = count($this->getHexesCoordinates());
        $hexesWithTokens = count(array_keys($this->getTokensForCompleteBoardByHex($playerId)));
        return $hexes - $hexesWithTokens <= 2;
    }

    function stNextPlayer() {
        $playerId = self::getActivePlayerId();
        if (!$playerId) {
            $this->activateNextPlayerCustom();
            $this->gamestate->nextState('nextPlayer');
            return;
        }

        //$this->setGameStateValue(TICKETS_USED, 0);
        $lastTurn = intval(self::getGameStateValue(LAST_TURN));

        // check if it was last action from the last player or if there is no arrow left
        if ($lastTurn == $playerId || ($this->hasReachedEndOfGameRequirements($playerId) && $this->isLastPlayer($playerId))) {
            $this->gamestate->nextState('endScore');
        } else {
            //finishing round or playing normally
            $this->refillCentralBoard();
            $this->refillAnimalCards();
            $this->activateNextPlayerCustom();
            $this->gamestate->nextState('nextPlayer');
        }
    }

    /**
     * Activates next player, also giving him extra time.
     */
    function activateNextPlayerCustom() {
        $player_id = $this->activeNextPlayer();
        self::setGameStateValue(TOOK_ANIMAL_CARD, 0);
        $this->deleteGlobalVariable(TOKENS_IN_HOLE);
        $this->setGlobalVariable(CAN_RESET_TURN, false);
        $this->giveExtraTime($player_id);
        $this->incStat(1, 'turns_number', $player_id);
        $this->incStat(1, 'turns_number');
        $this->notifyWithName('msg', clienttranslate('&#10148; Start of ${player_name}\'s turn'));
        $this->makeSavepoint();
    }


    function stEndScore() {
        $sql = "SELECT player_id id, player_score score, player_no playerNo FROM player ORDER BY player_no ASC";
        $players = self::getCollectionFromDb($sql);

        // points gained during the game
        $totalScore = [];
        foreach ($players as $playerId => $playerDb) {
            $totalScore[$playerId] = intval($playerDb['score']);
        }

        $roundScores = array_fill_keys(array_keys($players), 0);
        foreach ($this->getScoresTypes() as $goal) {
            foreach ($players as $playerId => $playerDb) {
                self::dump('*******************calculatingPoints', compact("goal", "playerId"));
                $board = $this->getBoard($playerId);
                switch ($goal["type"]) {
                    case TREES:
                        $score = $this->calculateTreePoints($board);
                        break;
                    case MOUTAINS:
                        $score = $this->calculateMountainsPoints($board);
                        break;
                    case FIELDS:
                        $score = $this->calculateFieldsPoints($board);
                        break;
                    case BUILDINGS:
                        $score = $this->calculateBuildingPoints($board);
                        break;
                    case WATER:
                        $score = $this->calculateWaterPoints($board);
                        break;
                    case ANIMAL_CARDS:
                        $cardsPoints = $this->calculateAnimalCardsPoints($playerId, $board);
                        $this->setGlobalVariable(CARDS_POINTS_FOR_PLAYER . $playerId, $cardsPoints);
                        $score = array_sum($cardsPoints);
                        foreach ($cardsPoints as $i => $cardScore) {
                            $index = $i + 1;
                            $scoreType = "score-card-$index-$playerId";
                            $this->incPlayerScore($playerId, $cardScore, "", ["scoreType" => $scoreType]);
                        }
                        $this->notifyPoints($playerId, $score, "", ["scoreType" => "score-total-2-$playerId"]);
                        break;

                    default:
                        $score = 0;
                        break;
                }
                self::dump('*******************calculatedGoalPoints', compact("goal", "score", "playerId"));
                self::incStat($score, $goal["stat"], $playerId);
                if ($goal["type"] != ANIMAL_CARDS) {
                    $this->incPlayerScore($playerId, $score, clienttranslate('${player_name} scores ${delta} points with ${source}'), ["color" => $this->getColorName($goal["type"]), "source" => $goal["nameTr"], "scoreType" => $this->getScoreName($goal["type"], $playerId)]);
                }
                $roundScores[$playerId] += $score;
                $totalScore[$playerId] += $score;
            }
        }
        foreach ($players as $playerId => $playerDb) {
            $this->notifyPoints($playerId, $this->calculateLandTotalFromStats($playerId), "", ["scoreType" => "score-total-1-$playerId"]);
            $this->notifyPoints($playerId, $totalScore[$playerId], "", ["scoreType" => "score-total-3-$playerId"]);
        }
        if ($this->getPlayerCount() == 1) {
            $soloPlayerId = array_keys($players)[0];
            $this->scoreSolo($soloPlayerId, $totalScore[$soloPlayerId]);
        }

        if ($this->isStudio()) {
            $this->gamestate->nextState('debugEndGame');
        } else {
            $this->gamestate->nextState('endGame');
        }
    }

    function scoreSolo($soloPlayerId, $totalScore){
        $suns = $this->convertScoreToSuns($totalScore);
        self::notifyWithName('msg', clienttranslate('${player_name} scores ${sunsCount} sun(s) for ${totalPoints} points'), ["sunsCount" => $suns, "totalPoints" => $totalScore,]);

        if ($this->isBoardSideA()) {
            $suns += 1;
            self::notifyWithName('msg', clienttranslate('${player_name} scores 1 sun for using side A of the board'));

        }
        if (!$this->isSpiritCardsOn()) {
            $suns += 2;
            self::notifyWithName('msg', clienttranslate('${player_name} scores 2 suns for not using spirit cards'));
        } else {
            $cards = $this->getAnimalCardsToScore($soloPlayerId);
            $spirits = array_filter($cards, fn ($c) => $c->isSpirit);
            $spirit = array_pop($spirits);
            if (in_array($spirit->type_arg, [33, 34, 37, 38, 41])) {
                $suns += 1;
                self::notifyWithName('msg', clienttranslate('${player_name} scores 1 sun for choosing a group spirit card'));
            }
        }

        self::DbQuery("UPDATE player SET player_score_aux = player_score WHERE player_id = $soloPlayerId");
        self::DbQuery("UPDATE player SET `player_score` = $suns where `player_id` = $soloPlayerId");
        $this->notifyPoints($soloPlayerId, $suns);
    }

    function calculateLandTotalFromStats($playerId) {
        return self::getStat("game_score_trees", $playerId)
            + self::getStat("game_score_mountains", $playerId)
            + self::getStat(
                "game_score_fields",
                $playerId
            )
            + self::getStat("game_score_buildings", $playerId)
            + self::getStat(
                "game_score_water",
                $playerId
            );
    }

    function getScoresTypes() {
        return [
            ["type" => TREES, "stat" => "game_score_trees", "nameTr" => clienttranslate("trees")],
            ["type" => MOUTAINS, "stat" => "game_score_mountains", "nameTr" => clienttranslate("mountains")],
            ["type" => FIELDS, "stat" => "game_score_fields", "nameTr" => clienttranslate("fields")],
            ["type" => BUILDINGS, "stat" => "game_score_buildings", "nameTr" => clienttranslate("buildings")],
            ["type" => WATER, "stat" => "game_score_water", "nameTr" => clienttranslate("water")],
            ["type" => ANIMAL_CARDS, "stat" => "game_animal_cards_score", "nameTr" => clienttranslate("animal cards")],
        ];
    }
}
