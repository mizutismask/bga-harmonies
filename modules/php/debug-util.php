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

            $this->animalCubes->pickCardsForLocation(1, 'deck', $hexId, "4");
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

    /*
   * loadBug: in studio, type loadBug(20762) into the table chat to load a bug report from production
   * client side JavaScript will fetch each URL below in sequence, then refresh the page
   */
    public function loadBug($reportId) {
        $db = explode('_', self::getUniqueValueFromDB("SELECT SUBSTRING_INDEX(DATABASE(), '_', -2)"));
        $game = $db[0];
        $tableId = $db[1];
        self::notifyAllPlayers('loadBug', "Trying to load <a href='https://boardgamearena.com/bug?id=$reportId' target='_blank'>bug report $reportId</a>", [
            'urls' => [
                // Emulates "load bug report" in control panel
                "https://studio.boardgamearena.com/admin/studio/getSavedGameStateFromProduction.html?game=$game&report_id=$reportId&table_id=$tableId",

                // Emulates "load 1" at this table
                "https://studio.boardgamearena.com/table/table/loadSaveState.html?table=$tableId&state=1",

                // Calls the function below to update SQL
                "https://studio.boardgamearena.com/1/$game/$game/loadBugSQL.html?table=$tableId&report_id=$reportId",

                // Emulates "clear PHP cache" in control panel
                // Needed at the end because BGA is caching player info
                "https://studio.boardgamearena.com/admin/studio/clearGameserverPhpCache.html?game=$game",
            ]
        ]);
    }

    /*
    * loadBugSQL: in studio, this is one of the URLs triggered by loadBug() above
    */
    public function loadBugSQL($reportId) {
        $studioPlayer = self::getCurrentPlayerId();
        $players = self::getObjectListFromDb("SELECT player_id FROM player", true);

        // Change for your game
        // We are setting the current state to match the start of a player's turn if it's already game over
        $sql = [
            "UPDATE global SET global_value=2 WHERE global_id=1 AND global_value=99"
        ];
        foreach ($players as $pId) {
            // All games can keep this SQL
            $sql[] = "UPDATE player SET player_id=$studioPlayer WHERE player_id=$pId";
            $sql[] = "UPDATE global SET global_value=$studioPlayer WHERE global_value=$pId";
            $sql[] = "UPDATE stats SET stats_player_id=$studioPlayer WHERE stats_player_id=$pId";
            $sql[] = "UPDATE gamelog SET gamelog_player=$studioPlayer WHERE gamelog_player=$pId";
            $sql[] = "UPDATE gamelog SET gamelog_current_player=$studioPlayer WHERE gamelog_current_player=$pId";
            $sql[] = "UPDATE gamelog SET gamelog_notification=REPLACE(gamelog_notification, $pId, $studioPlayer)";

            // TODO Add game-specific SQL updates for the tables, everywhere players ids are used in your game 
            $sql[] = "UPDATE animalCard SET card_location=REPLACE(card_location, $pId, $studioPlayer)";
            $sql[] = "UPDATE animalCube SET card_location=REPLACE(card_location, $pId, $studioPlayer)";
            $sql[] = "UPDATE coloredToken SET card_location=REPLACE(card_location, $pId, $studioPlayer)";
            $sql[] = "UPDATE global_variables SET name=REPLACE(name, $pId, $studioPlayer)";

            // This could be improved, it assumes you had sequential studio accounts before loading
            // e.g., quietmint0, quietmint1, quietmint2, etc. are at the table
            $studioPlayer++;
        }
        $msg = "<b>Loaded <a href='https://boardgamearena.com/bug?id=$reportId' target='_blank'>bug report $reportId</a></b><hr><ul><li>" . implode(';</li><li>', $sql) . ';</li></ul>';
        self::warn($msg);
        self::notifyAllPlayers('message', $msg, []);

        foreach ($sql as $q) {
            self::DbQuery($q);
        }
        self::reloadPlayersBasicInfos();
        $this->gamestate->reloadState();
    }
}
