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
        $this->refillAnimalCards();

        foreach ($playersIds as $playerId) {
            //$this->pickInitialDestinationCards($playerId);
        }

        $this->gamestate->nextState('');
    }

    function hasReachedEndOfGameRequirements($playerId): bool {
        $playersIds = $this->getPlayersIds();

        $end = false; //todo
        if ($end && intval(self::getGameStateValue(LAST_TURN) == 0)) {
            self::setGameStateValue(LAST_TURN, $this->getLastPlayer()); //we play until the last player to finish the round
            if (!$this->isLastPlayer($playerId)) {
                self::notifyWithName('lastTurn', clienttranslate('${player_name} has no more destination cards, finishing round !'), []);
            }
        }
        return $end;
    }

    function stNextPlayer() {
        $playerId = self::getActivePlayerId();

        //$this->setGameStateValue(TICKETS_USED, 0);
        $lastTurn = intval(self::getGameStateValue(LAST_TURN));

        // check if it was last action from the last player or if there is no arrow left
        if ($lastTurn == $playerId || ($this->hasReachedEndOfGameRequirements($playerId) && $this->isLastPlayer($playerId))) {
            $this->gamestate->nextState('endScore');
        } else {
            //finishing round or playing normally
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
        $this->giveExtraTime($player_id);
        $this->incStat(1, 'turns_number', $player_id);
        $this->incStat(1, 'turns_number');
        $this->notifyWithName('msg', clienttranslate('&#10148; Start of ${player_name}\'s turn'));
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
                $board = $this->getGrid($playerId);
                switch ($goal["type"]) {
                    case TREES:
                        $score = $this->calculateTreePoints($board);
                        break;
                    case MOUTAINS:
                        $score = $this->calculateTreePoints($board); //todo
                        break;
                    case FIELDS:
                        $score = $this->calculateTreePoints($board); //todo
                        break;
                    case BUILDINGS:
                        $score = $this->calculateBuildingPoints($board); //todo
                        break;
                    case ANIMAL_CARDS:
                        $score = $this->calculateAnimalCardsPoints($board);
                        break;

                    default:
                        $score = 0;
                        break;
                }
                self::dump('*******************calculatedGoalPoints', compact("goal", "score","playerId"));
                self::incStat($score, $goal["stat"], $playerId);
                $this->incPlayerScore($playerId, $score, clienttranslate('${player_name} scores ${delta} points with ${source}'), ["color" => $this->getColorName($goal->color), "source" => $goal["nameTr"], "scoreType" => $this->getScoreType($goal[["type"]], $playerId)]);
                $roundScores[$playerId] += $score;
                $totalScore[$playerId] += $score;
            }
        }

        if ($this->isStudio()) {
            $this->gamestate->nextState('debugEndGame');
        } else {
            $this->gamestate->nextState('endGame');
        }
    }
}
