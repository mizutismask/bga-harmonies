<?php
require_once('./gameBaseTest.php');

class ScoreWaterSideATest extends GameTestBase { // this is your game class defined in ggg.game.php
    function __construct() {
        // parent::__construct();
        include '../material.inc.php'; // this is how this normally included, from constructor
    }
    public function isBoardSideA() {
        return true;
    }

    // class tests
    function testScoreWaterFromRules() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 1, 2, [BLUE]);
        $this->setTokensIn($grid, 1, 1, [BLUE]);
        $this->setTokensIn($grid, 1, 0, [BLUE]);
        $this->setTokensIn($grid, 2, 1, [BLUE]);
        $this->setTokensIn($grid, 3, 1, [BLUE]);
        $this->setTokensIn($grid, 3, 2, [BLUE]);
        $this->setTokensIn($grid, 4, 3, [BLUE]);

        $result = $this->calculateWaterPoints($grid);

        $equal = $result == 15;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }



    function testAll() {
        $this->testScoreWaterFromRules();
    }
}

$test1 = new ScoreWaterSideATest();
$test1->testAll();
