<?php
require_once('./gameBaseTest.php');

class ScoreBuildingSpiritTest extends GameTestBase { // this is your game class defined in ggg.game.php
    function __construct() {
        // parent::__construct();
        include '../material.inc.php'; // this is how this normally included, from constructor
    }

    /** Redefine some function of the game to mock data. Todo : rename getData to match your function */
    function getBoard($playerId = null) {
        $grid = $this->initBoard();
        if ($playerId == 1) {
            $this->setTokensIn($grid, 3, 0, [RED, GRAY]);

            $this->setTokensIn($grid, 2, 3, [RED, BROWN]);
            $this->setTokensIn($grid, 0, 3, [RED]);
            $this->setTokensIn($grid, 4, 2, [RED, RED]);
        } else if ($playerId == 38) {
            $this->setTokensIn($grid, 3, 0, [RED, GRAY]);

            $this->setTokensIn($grid, 2, 2, [RED, BROWN]);
            $this->setTokensIn($grid, 2, 3, [RED, BROWN]);
            $this->setTokensIn($grid, 4, 1, [RED, RED]);

            $this->setTokensIn($grid, 0, 2, [RED]);
            $this->setTokensIn($grid, 0, 3, [RED, BROWN]);
        }
        return $grid;
    }

    function getPlayersIds() {
        return [
            1, 2
        ];
    }

    // class tests
    function testCalculatePointsForSpiritCard37() {

        $grid = $this->getBoard(1);

        $card = $this->ANIMAL_CARDS[1][37];
        $card->type_arg = 37;
        $result = $this->calculatePointsForSpiritCard("player", $card, $grid);

        $equal = $result == 12;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testCalculatePointsForSpiritCard38() {

        $grid = $this->getBoard(38);

        $card = $this->ANIMAL_CARDS[1][38];
        $card->type_arg = 38;
        $result = $this->calculatePointsForSpiritCard("player", $card, $grid);

        $equal = $result == 12;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testAll() {
        $this->testCalculatePointsForSpiritCard37();
        $this->testCalculatePointsForSpiritCard38();
    }
}

$test1 = new ScoreBuildingSpiritTest();
$test1->testAll();
