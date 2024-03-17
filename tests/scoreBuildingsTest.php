<?php
require_once('./gameBaseTest.php');

class ScoreBuildingsTest extends GameTestBase { // this is your game class defined in ggg.game.php
    function __construct() {
        // parent::__construct();
        include '../material.inc.php'; // this is how this normally included, from constructor
    }

    /** Redefine some function of the game to mock data. Todo : rename getData to match your function */
    function getBoard($playerId = null) {
        $grid = $this->initBoard();
        if ($playerId == 1) {
            $this->setTokensIn($grid, 3, 1, [GRAY, RED]);

            $this->setTokensIn($grid, 2, 1, [BROWN, RED]);
            $this->setTokensIn($grid, 4, 1, [BLUE]);
            $this->setTokensIn($grid, 3, 2, [YELLOW]);
        }
        return $grid;
    }

    function getPlayersIds() {
        return [
            1, 2
        ];
    }

    // class tests
    function testIsHexSurroundedBy3DifferentColors() {

        $result = $this->isHexSurroundedBy3DifferentColors($this->getBoard(1), ["col" => 3, "row" => 1]);

        $equal = $result == true;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testAll() {
        $this->testIsHexSurroundedBy3DifferentColors();
    }
}

$test1 = new ScoreBuildingsTest();
$test1->testAll();
