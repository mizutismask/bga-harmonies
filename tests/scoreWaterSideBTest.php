<?php
require_once('./gameBaseTest.php');

class ScoreWaterSideBTest extends GameTestBase { // this is your game class defined in ggg.game.php
    function __construct() {
        // parent::__construct();
        include '../material.inc.php'; // this is how this normally included, from constructor
    }

    // class tests
    function testScoreWaterFromRules() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 0, 1, [BLUE]);
        $this->setTokensIn($grid, 1, 0, [BLUE]);
        $this->setTokensIn($grid, 2, 1, [BLUE]);
        $this->setTokensIn($grid, 3, 1, [BLUE]);
        $this->setTokensIn($grid, 3, 2, [BLUE]);
        $this->setTokensIn($grid, 2, 3, [BLUE]);
        $this->setTokensIn($grid, 4, 1, [BLUE]);
        $this->setTokensIn($grid, 5, 0, [BLUE]);
        $this->setTokensIn($grid, 6, 0, [BLUE]);
        $this->setTokensIn($grid, 6, 1, [BLUE]);

        $result = $this->calculateWaterPoints($grid);

        $equal = $result == 20;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }



    function testAll() {
        $this->testScoreWaterFromRules();
    }
}

$test1 = new ScoreWaterSideBTest();
$test1->testAll();
