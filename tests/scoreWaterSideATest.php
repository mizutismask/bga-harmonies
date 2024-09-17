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

    function testScoreWaterWithAdditionalTokens() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 1, 2, [BLUE]);
        $this->setTokensIn($grid, 1, 1, [BLUE]);
        $this->setTokensIn($grid, 1, 0, [BLUE]);
        $this->setTokensIn($grid, 2, 1, [BLUE]);
        $this->setTokensIn($grid, 3, 1, [BLUE]);
        $this->setTokensIn($grid, 3, 2, [BLUE]);
        $this->setTokensIn($grid, 4, 3, [BLUE]);

        $this->setTokensIn($grid, 4, 4, [BLUE]);
        $this->setTokensIn($grid, 1, 3, [BLUE]);

        $result = $this->calculateWaterPoints($grid);

        $equal = $result == 23;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testScoreWaterWithUnclearPath() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 1, 2, [BLUE]);
        $this->setTokensIn($grid, 1, 1, [BLUE]);
        $this->setTokensIn($grid, 1, 0, [BLUE]);
        $this->setTokensIn($grid, 2, 1, [BLUE]);
        $this->setTokensIn($grid, 3, 1, [BLUE]);
        $this->setTokensIn($grid, 3, 2, [BLUE]);
        $this->setTokensIn($grid, 4, 3, [BLUE]);

        $this->setTokensIn($grid, 2, 2, [BLUE]);
        $this->setTokensIn($grid, 4, 1, [BLUE]);

        $result = $this->calculateWaterPoints($grid);

        $equal = $result == 11;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testScoreWaterZigZag() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 2, 0, [BLUE]);
        $this->setTokensIn($grid, 4, 1, [BLUE]);
        
        $this->setTokensIn($grid, 0, 3, [BLUE]);
        $this->setTokensIn($grid, 1, 3, [BLUE]);
        $this->setTokensIn($grid, 2, 3, [BLUE]);
        $this->setTokensIn($grid, 3, 3, [BLUE]);
        $this->setTokensIn($grid, 4, 3, [BLUE]);

        $result = $this->calculateWaterPoints($grid);

        $equal = $result == 11;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testScoreWaterCircular() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 1, 0, [BLUE]);
        $this->setTokensIn($grid, 1, 1, [BLUE]);
        $this->setTokensIn($grid, 2, 0, [BLUE]);
        $this->setTokensIn($grid, 2, 2, [BLUE]);
        $this->setTokensIn($grid, 3, 0, [BLUE]);
        $this->setTokensIn($grid, 3, 1, [BLUE]);

        $result = $this->calculateWaterPoints($grid);

        $equal = $result == 15;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testScoreWaterCircularPlusOneOnSide() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 1, 0, [BLUE]);
        $this->setTokensIn($grid, 1, 1, [BLUE]);
        $this->setTokensIn($grid, 2, 0, [BLUE]);
        $this->setTokensIn($grid, 2, 2, [BLUE]);
        $this->setTokensIn($grid, 3, 0, [BLUE]);
        $this->setTokensIn($grid, 3, 1, [BLUE]);
        $this->setTokensIn($grid, 4, 1, [BLUE]);

        $result = $this->calculateWaterPoints($grid);

        $equal = $result == 15;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testScoreWaterDoubleCircular() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 1, 0, [BLUE]);
        $this->setTokensIn($grid, 1, 1, [BLUE]);
        $this->setTokensIn($grid, 2, 0, [BLUE]);
        $this->setTokensIn($grid, 2, 2, [BLUE]);
        $this->setTokensIn($grid, 3, 0, [BLUE]);
        $this->setTokensIn($grid, 3, 1, [BLUE]);

        $this->setTokensIn($grid, 1, 2, [BLUE]);
        $this->setTokensIn($grid, 2, 3, [BLUE]);
        $this->setTokensIn($grid, 3, 2, [BLUE]);

        $result = $this->calculateWaterPoints($grid);

        $equal = $result == 15;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testAll() {
        $this->testScoreWaterFromRules();
        $this->testScoreWaterWithAdditionalTokens();
        $this->testScoreWaterWithUnclearPath();
        $this->testScoreWaterZigZag();
        $this->testScoreWaterCircular();
        $this->testScoreWaterCircularPlusOneOnSide();
        //$this->testScoreWaterDoubleCircular();//not sure how it should count, will probably never happen
    }
}

$test1 = new ScoreWaterSideATest();
$test1->testAll();
