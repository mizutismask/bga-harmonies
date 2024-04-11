<?php
define("APP_GAMEMODULE_PATH", "../misc/"); // include path to stubs, which defines "table.game.php" and other classes
require_once('./gameBaseTest.php');

class GameTest extends GameTestBase { // this is your game class defined in ggg.game.php
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
    function testYourTestNameColorsAllowed() {
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(BLUE, BLUE) == false, $this->isColorAllowedOnTopOfOtherColor(BLUE, BLUE));
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(GREEN, GREEN) == false, $this->isColorAllowedOnTopOfOtherColor(GREEN, GREEN));
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(YELLOW, YELLOW) == false, $this->isColorAllowedOnTopOfOtherColor(YELLOW, YELLOW));
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(GREEN, GRAY) == false, $this->isColorAllowedOnTopOfOtherColor(GREEN, GRAY));
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(GREEN, RED) == false, $this->isColorAllowedOnTopOfOtherColor(GREEN, RED));
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(YELLOW, BROWN) == false, $this->isColorAllowedOnTopOfOtherColor(YELLOW, BROWN));
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(BLUE, BROWN) == false, $this->isColorAllowedOnTopOfOtherColor(BLUE, BROWN));
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(GRAY, BROWN) == false, $this->isColorAllowedOnTopOfOtherColor(GRAY, BROWN));

        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(GREEN, BROWN) == true, $this->isColorAllowedOnTopOfOtherColor(GREEN, BROWN));
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(GRAY, GRAY) == true, $this->isColorAllowedOnTopOfOtherColor(GRAY, GRAY));
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(RED, RED) == true, $this->isColorAllowedOnTopOfOtherColor(RED, RED));
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(RED, GRAY) == true, $this->isColorAllowedOnTopOfOtherColor(RED, GRAY));
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(RED, BROWN) == true, $this->isColorAllowedOnTopOfOtherColor(RED, BROWN));
        $this->displayResult(__FUNCTION__, $this->isColorAllowedOnTopOfOtherColor(BROWN, BROWN) == true, $this->isColorAllowedOnTopOfOtherColor(RED, BROWN));
    }
    /*function testYourTestNameColorsAllowed() {

       $result = $this->isColorAllowedOnTopOfOtherColor());

        $equal = $result == null;

        $this->displayResult(__FUNCTION__, $equal, $result);
        
    }*/
    function testMaterialCardDescription() {

        foreach ($this->ANIMAL_CARDS[1] as $i => $animalCardInfo) {
            $this->systemAssertTrue("No position 0 in card of type " . $i, $animalCardInfo->pattern[0]->position === 0);
            $this->systemAssertTrue("No cube allowed in card of type " . $i, $this->array_some($animalCardInfo->pattern, fn ($patHex) => $patHex->allowCube === true));
        }
    }

    function testAll() {
        $this->testYourTestNameColorsAllowed();
        $this->testMaterialCardDescription();
    }
}

$test1 = new GameTest();
$test1->testAll();
