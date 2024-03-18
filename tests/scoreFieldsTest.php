<?php
require_once('./gameBaseTest.php');

class ScoreFieldsTest extends GameTestBase { // this is your game class defined in ggg.game.php
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
    function testScoreFieldsOnlySize1Zones() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 3, 1, [YELLOW]);
        $this->setTokensIn($grid, 1, 0, [YELLOW]);
        $this->setTokensIn($grid, 6, 0, [YELLOW]);
        $this->setTokensIn($grid, 1, 2, [YELLOW]);

        $result = $this->calculateFieldsPoints($grid);

        $equal = $result == 0;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testScoreFields1Size2() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 3, 1, [YELLOW]);
        $this->setTokensIn($grid, 4, 2, [YELLOW]);

        $result = $this->calculateFieldsPoints($grid);

        $equal = $result == 5;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testScoreFields1Size3() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 3, 1, [YELLOW]);
        $this->setTokensIn($grid, 4, 2, [YELLOW]);
        $this->setTokensIn($grid, 5, 1, [YELLOW]);

        $result = $this->calculateFieldsPoints($grid);

        $equal = $result == 5;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testScoreFields2Size2() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 3, 1, [YELLOW]);
        $this->setTokensIn($grid, 3, 0, [YELLOW]);

        $this->setTokensIn($grid, 1, 0, [YELLOW]);
        $this->setTokensIn($grid, 1, 1, [YELLOW]);

        $result = $this->calculateFieldsPoints($grid);

        $equal = $result == 10;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testScoreFieldsOneOK2KO() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 3, 1, [YELLOW]);
        $this->setTokensIn($grid, 3, 0, [YELLOW]);

        $this->setTokensIn($grid, 1, 0, [YELLOW]);
        $this->setTokensIn($grid, 5, 1, [YELLOW]);

        $result = $this->calculateFieldsPoints($grid);

        $equal = $result == 5;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testAll() {
        $this->testScoreFieldsOnlySize1Zones();
        $this->testScoreFields1Size2();
        $this->testScoreFields2Size2();
        $this->testScoreFieldsOneOK2KO();
    }
}

$test1 = new ScoreFieldsTest();
$test1->testAll();
