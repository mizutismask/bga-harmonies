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

    function testAll() {
        $this->testGetNeighboursInCenter();
        $this->testGetNeighboursOnTop();
        $this->testGetNeighboursOnRightBottom();
    }
}

$test1 = new HoneyCombTest();
$test1->testAll();
