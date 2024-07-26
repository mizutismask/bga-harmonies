<?php

trait DebugUtilTrait {

    //////////////////////////////////////////////////////////////////////////////
    //////////// Utility functions
    ////////////

    function debugSetup() {
        if (!$this->isStudio()) {
            return;
        }

        $this->gamestate->changeActivePlayer(2333092);
        $this->fill();
    }

    /**
     * Fills the current players board with random piles of tokens and add a cube on each.
     */
    function fill() {
        $hexes = $this->getHexesCoordinates();
        $playerId = $this->getCurrentPlayerId();
        foreach ($hexes as $hex) {
            $hexId = $this->getCellName($hex, $playerId);
            $number = bga_rand(1, 3);
            $tokens = array_slice($this->coloredTokens->getCardsInLocation("deck"), 0, $number);
            foreach ($tokens as $token) {
                $this->moveColoredTokenToBoard($token["id"], $hexId);
            }

            $this->animalCubes->pickCardsForLocation(rand(0,1), 'deck', $hexId, "4");
        }
    }

    /**
     * Fills the current players board with random piles of tokens and add a cube on each but leaves 3 spaces empty.
     */
    function almostFill() {
        $hexes = $this->getHexesCoordinates();
        $playerId = $this->getCurrentPlayerId();
        $i = 0;
        foreach ($hexes as $hex) {
            if ($i < count($hexes) - 3) {
                $hexId = $this->getCellName($hex, $playerId);
                $number = bga_rand(1, 3);
                $tokens = array_slice($this->coloredTokens->getCardsInLocation("deck"), 0, $number);
                foreach ($tokens as $token) {
                    $this->moveColoredTokenToBoard($token["id"], $hexId);
                }

                $this->animalCubes->pickCardsForLocation(1, 'deck', $hexId, "4");
            }
            $i++;
        }
    }

    /**
     * Removes everything from the current players board.
     */
    function clear() {
        self::DbQuery("UPDATE `coloredToken` set `card_location` = 'deck'");
        self::DbQuery("UPDATE `animalCube` set `card_location` = 'deck'");
    }

    /* public function debugReplacePlayersIds() {
        if (!$this->isStudio() ) {
            return;
        }

        // These are the id's from the BGAtable I need to debug.
        // SELECT JSON_ARRAYAGG(`player_id`) FROM `player`
        $ids = [90574255, 93146640];

        // Id of the first player in BGA Studio
        $sid = 2343492;

        foreach ($ids as $id) {
            // basic tables
            $this->DbQuery("UPDATE player SET player_id=$sid WHERE player_id = $id");
            $this->DbQuery("UPDATE global SET global_value=$sid WHERE global_value = $id");
            $this->DbQuery("UPDATE stats SET stats_player_id=$sid WHERE stats_player_id = $id");

            // 'other' game specific tables. example:
            // tables specific to your schema that use player_ids
            $this->DbQuery("UPDATE traincar SET card_location_arg=$sid WHERE card_location_arg = $id");
            $this->DbQuery("UPDATE destination SET card_location_arg=$sid WHERE card_location_arg = $id");
            $this->DbQuery("UPDATE claimed_routes SET player_id=$sid WHERE player_id = $id");

            ++$sid;
        }
    }*/

    function debug($debugData) {
        if (!$this->isStudio()) {
            return;
        }
        die('debug data : ' . json_encode($debugData));
    }

    function endGame() {
        $this->gamestate->nextState("endGame");
    }

    public function loadBugReportSQL(int $reportId, array $studioPlayers): void {
        $prodPlayers = $this->getObjectListFromDb("SELECT `player_id` FROM `player`", true);
        $prodCount = count($prodPlayers);
        $studioCount = count($studioPlayers);
        if ($prodCount != $studioCount) {
            throw new BgaVisibleSystemException("Incorrect player count (bug report has $prodCount players, studio table has $studioCount players)");
        }

        // SQL specific to your game
        // For example, reset the current state if it's already game over
        $sql = [
            "UPDATE `global` SET `global_value` = 10 WHERE `global_id` = 1 AND `global_value` = 99"
        ];
        foreach ($prodPlayers as $index => $prodId) {
            $studioId = $studioPlayers[$index];
            // SQL common to all games
            $sql[] = "UPDATE `player` SET `player_id` = $studioId WHERE `player_id` = $prodId";
            $sql[] = "UPDATE `global` SET `global_value` = $studioId WHERE `global_value` = $prodId";
            $sql[] = "UPDATE `stats` SET `stats_player_id` = $studioId WHERE `stats_player_id` = $prodId";
            $sql[] = "UPDATE gamelog SET gamelog_player=$studioId WHERE gamelog_player=$prodId";
            $sql[] = "UPDATE gamelog SET gamelog_current_player=$studioId WHERE gamelog_current_player=$prodId";
            $sql[] = "UPDATE gamelog SET gamelog_notification=REPLACE(gamelog_notification, $prodId, $studioId)";

            // SQL specific to your game
            $sql[] = "UPDATE animalCard SET card_location=REPLACE(card_location, $prodId, $studioId)";
            $sql[] = "UPDATE animalCube SET card_location=REPLACE(card_location, $prodId, $studioId)";
            $sql[] = "UPDATE coloredToken SET card_location=REPLACE(card_location, $prodId, $studioId)";
            $sql[] = "UPDATE global_variables SET name=REPLACE(name, $prodId, $studioId)";
        }
        foreach ($sql as $q) {
            $this->DbQuery($q);
        }
    }
}
