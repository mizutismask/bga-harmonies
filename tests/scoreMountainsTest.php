<?php
require_once('./gameBaseTest.php');

class ScoreMountainsTest extends GameTestBase { // this is your game class defined in ggg.game.php
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
    function testScoreLonelyMoutain() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 3, 1, [GRAY, GRAY, GRAY]);

        $result = $this->calculateMountainsPoints($grid);

        $equal = $result == 0;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testScoreMoutains2StacksOf3() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 3, 1, [GRAY, GRAY, GRAY]);
        $this->setTokensIn($grid, 4, 2, [GRAY, GRAY, GRAY]);

        $result = $this->calculateMountainsPoints($grid);

        $equal = $result == 14;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testScoreMoutains3DifferentStacks() {

        $grid = $this->initBoard();
        $this->setTokensIn($grid, 3, 1, [GRAY]);
        $this->setTokensIn($grid, 4, 2, [GRAY, GRAY]);
        $this->setTokensIn($grid, 5, 2, [GRAY, GRAY, GRAY]);

        $result = $this->calculateMountainsPoints($grid);

        $equal = $result == 11;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testAll() {
        $this->testScoreLonelyMoutain();
        $this->testScoreMoutains2StacksOf3();
        $this->testScoreMoutains3DifferentStacks();
    }
}

$test1 = new ScoreMountainsTest();
$test1->testAll();
