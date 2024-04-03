<?php
define("APP_GAMEMODULE_PATH", "../misc/"); // include path to stubs, which defines "table.game.php" and other classes
require_once('./gameBaseTest.php');

class HoneyCombTest extends GameTestBase { // this is your game class defined in ggg.game.php
    function __construct() {
        // parent::__construct();
        include '../material.inc.php'; // this is how this normally included, from constructor
    }

    /** Redefine some function of the game to mock data. Todo : rename getData to match your function */
    function getData($playerId = null) {

        return [];
    }

    function getPlayersIds() {
        return [
            1, 2, 3
        ];
    }

    // class tests
    function testGetNeighboursInCenter() {

        $hex = ["col" => 3, "row" => 1];
        $result = $this->getNeighbours($hex);
        $this->displayResult(__FUNCTION__, 6, count($result));

        $equal = $this->containsHex($result, 2, 1);
        $this->displayResult(__FUNCTION__, $equal, $result);
        $equal = $this->containsHex($result, 3, 0);
        $this->displayResult(__FUNCTION__, $equal, $result);
        $equal = $this->containsHex($result, 4, 1);
        $this->displayResult(__FUNCTION__, $equal, $result);
        $equal = $this->containsHex($result, 4, 2);
        $this->displayResult(__FUNCTION__, $equal, $result);
        $equal = $this->containsHex($result, 3, 2);
        $this->displayResult(__FUNCTION__, $equal, $result);
        $equal = $this->containsHex($result, 2, 2);
        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testGetNeighboursOnTop() {

        $hex = ["col" => 4, "row" => 0];
        $result = $this->getNeighbours($hex);
        $this->displayResult(__FUNCTION__, 3, count($result));

        $equal = $this->containsHex($result, 5, 0);
        $this->displayResult(__FUNCTION__, $equal, $result);
        $equal = $this->containsHex($result, 4, 1);
        $this->displayResult(__FUNCTION__, $equal, $result);
        $equal = $this->containsHex($result, 3, 0);
    }

    function testGetNeighboursOnRightBottom() {

        $hex = ["col" => 6, "row" => 3];
        $result = $this->getNeighbours($hex);
        $this->displayResult(__FUNCTION__, 2, count($result));

        $equal = $this->containsHex($result, 5, 2);
        $this->displayResult(__FUNCTION__, $equal, $result);
        $equal = $this->containsHex($result, 6, 2);
    }

    function testGetNeighboursOnTopLeft() {

        $hex = ["col" => 0, "row" => 0];
        $result = $this->getNeighbours($hex);
        $this->displayResult(__FUNCTION__, 2, count($result));

        $equal = $this->containsHex($result, 0, 1);
        $this->displayResult(__FUNCTION__, $equal, $result);
        $equal = $this->containsHex($result, 1, 0);
        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testAreExpectedTokensInHexTrue() {

        $board = $this->initBoard();
        $this->setTokensIn($board, 1, 1, [
            GREEN, BROWN, BROWN
        ]);

        $result = $this->areExpectedTokensInHex(
            $board,
            1,
            1,
            [GREEN, BROWN, BROWN]
        );
        $equal = $result == true;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testAreExpectedTokensInHexWithBuilding() {

        $board = $this->initBoard();
        $this->setTokensIn($board, 1, 1, [
            RED, GRAY,
        ]);

        $result = $this->areExpectedTokensInHex(
            $board,
            1,
            1,
            [RED, BUILDING]
        );
        $equal = $result == true;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testAreExpectedTokensInHexFalse() {

        $board = $this->initBoard();
        $this->setTokensIn($board, 1, 1, [GREEN, BROWN]);

        $result = $this->areExpectedTokensInHex(
            $board,
            1,
            1,
            [BROWN, BROWN]
        );
        $equal = $result == false;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testAreExpectedTokensInHexMissingOne() {

        $board = $this->initBoard();
        $this->setTokensIn($board, 1, 1, [GREEN, BROWN]);

        $result = $this->areExpectedTokensInHex(
            $board,
            1,
            1,
            [GREEN, BROWN, BROWN]
        );
        $equal = $result == false;

        $this->displayResult(__FUNCTION__, $equal, $result);
    }

    function testGetPossibleLocationsForCubeInPattern() {

        $board = $this->initBoard();
        $this->setTokensIn($board, 0, 0, [
            RED, GRAY,
        ]);
        $this->setTokensIn($board, 1, 0, [BLUE]);
        $this->setTokensIn($board, 2, 1, [BLUE]);

        $result = $this->getPossibleLocationsForCubeInPattern($board, $this->ANIMAL_CARDS[1][6]);

        $equal = $this->containsHex($result, 1, 0) == true;
        $this->displayResult(__FUNCTION__, $equal, $result);

        $equal = count($result) == 1;
        $this->displayResult(__FUNCTION__, $equal, count($result));
    }

    function testGetPossibleLocationsForCubeInPatternIncomplete() {

        $board = $this->initBoard();
        $this->setTokensIn($board, 0, 0, [GRAY]);

        $result = $this->getPossibleLocationsForCubeInPattern($board, $this->ANIMAL_CARDS[1][2]);

        $equal = count($result) == 0;
        $this->displayResult(__FUNCTION__, $equal, count($result));
    }

    function testGetPossibleLocationsForCubeInPatternOnBoardSide() {

        $board = $this->initBoard();
        $this->setTokensIn($board, 2, 1, [GREEN]);
        $this->setTokensIn($board, 3, 1, [GREEN]);
        $this->setTokensIn($board, 4, 2, [BLUE]);

        $result = $this->getPossibleLocationsForCubeInPattern($board, $this->ANIMAL_CARDS[1][4]);

        $equal = $this->containsHex($result, 4, 2) == true;
        $this->displayResult(__FUNCTION__, $equal, $result);

        $equal = count($result) == 1;
        $this->displayResult(__FUNCTION__, $equal, count($result));
    }

    function testGetPossibleLocationsForCubeInPatternCard4() {

        $board = $this->initBoard();

        $this->setTokensIn($board, 1, 1, [GREEN]);
        $this->setTokensIn($board, 2, 2, [GREEN]);
        $this->setTokensIn($board, 3, 2, [BLUE]);

        $result = $this->getPossibleLocationsForCubeInPattern($board, $this->ANIMAL_CARDS[1][4]);

        $equal = $this->containsHex($result, 3, 2) == true;
        $this->displayResult(__FUNCTION__, $equal, $result);

        $equal = count($result) == 1;
        $this->displayResult(__FUNCTION__, $equal, count($result));
    }

    function testGetPossibleLocationsForCubeInRotatedPatternCard5() {

        $board = $this->initBoard();

        $this->setTokensIn($board, 0, 0, [BLUE]);
        $this->setTokensIn($board, 1, 0, [GREEN]);

        $result = $this->getPossibleLocationsForCubeInPattern($board, $this->ANIMAL_CARDS[1][5]);

        $equal = $this->containsHex($result, 0, 0) == true;
        $this->displayResult(__FUNCTION__, $equal, $result);

        $equal = count($result) == 1;
        $this->displayResult(__FUNCTION__, $equal, count($result));
    }

    function testGetPatternRotations() {
        $result = $this->getPatternRotations($this->ANIMAL_CARDS[1][9]->pattern);
        //echo  json_encode($result);
    }

    function testGetAdjacentHexCoordinate() {

        $this->expectHexResult(
            $this->getAdjacentHexCoordinate([
                "col" => 1, "row" => 1
            ], 3),
            2,
            2,
            __FUNCTION__
        );

        $this->expectHexResult(
            $this->getAdjacentHexCoordinate([
                "col" => 1, "row" => 1
            ], 2),
            2,
            1,
            __FUNCTION__
        );

        $this->expectHexResult(
            $this->getAdjacentHexCoordinate([
                "col" => 0, "row" => 0
            ], 3),
            1,
            0,
            __FUNCTION__
        );

        $this->expectHexResult(
            $this->getAdjacentHexCoordinate([
                "col" => 0, "row" => 4
            ], 2),
            1,
            3,
            __FUNCTION__
        );
    }

    function testAll() {
        $this->testGetNeighboursInCenter();
        $this->testGetNeighboursOnTop();
        $this->testGetNeighboursOnRightBottom();
        $this->testAreExpectedTokensInHexTrue();
        $this->testAreExpectedTokensInHexFalse();
        $this->testAreExpectedTokensInHexMissingOne();
        $this->testAreExpectedTokensInHexWithBuilding();
        $this->testGetPossibleLocationsForCubeInPattern();
        $this->testGetPossibleLocationsForCubeInPatternIncomplete();
        $this->testGetPossibleLocationsForCubeInPatternOnBoardSide();
        $this->testGetPossibleLocationsForCubeInPatternCard4();
        $this->testGetNeighboursOnTopLeft();
        $this->testGetAdjacentHexCoordinate();
        $this->testGetPatternRotations();
        $this->testGetPossibleLocationsForCubeInRotatedPatternCard5();
    }
}

$test1 = new HoneyCombTest();
$test1->testAll();
