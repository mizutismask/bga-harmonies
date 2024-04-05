<?php

require_once(__DIR__ . '/objects/animalCube.php');
require_once(__DIR__ . '/objects/animalCard.php');

trait AnimalCubeDeckTrait {

    /**
     * Create animal cubes.
     */
    public function createAnimalCubes() {
        $tokens = array(
            array('type' => 1, 'type_arg' => 1, 'nbr' => 66),
        );
        if ($this->isSpiritCardsOn()) {
            $tokens[] =  array('type' => 1, 'type_arg' => 2, 'nbr' => $this->getPlayerCount());
        }
        $this->animalCubes->createCards($tokens, 'deck');
    }

    /**
     * Pick tokens to fill an animal card.
     */
    public function fillAnimalCard(AnimalCard $card) {
        $cubes = [];
        $isSpirit = $card->isSpirit;

        if ($isSpirit) {
            $spiritCubes = $this->getCardsOfTypeArgFromLocation("animalCube", 2, "deck");
            $this->systemAssertTrue("There is not spirit cube available", count($spiritCubes) > 0);
            $cube = array_pop($spiritCubes);
            $this->animalCubes->moveCard($cube["id"], "card_" . $card->id, 0);
            $cubes[] = $this->animalCubes->getCard($cube["id"]); //refresh
        } else {
            for ($i = 0; $i < count($card->pointLocations); $i++) {
                $normalCubes = $this->getCardsOfTypeArgFromLocation("animalCube", 1, "deck");
                $cube = array_pop($normalCubes);
                $this->animalCubes->moveCard($cube["id"], "card_" . $card->id, $i);
                $cubes[] = $this->animalCubes->getCard($cube["id"]); //refresh
            }
        }
        $this->notifyAllPlayers('materialMove', "", [
            'type' => MATERIAL_TYPE_CUBE,
            'from' => MATERIAL_LOCATION_DECK,
            'to' => MATERIAL_LOCATION_CARD,
            'toArg' => $card->id,
            'material' => $this->getAnimalCubesFromDb($cubes),
            //'context' => $isSpirit ? "cubespirit" : "",
        ]);
    }

    public function moveCubeToHex($cubeId, $hexId, $fromCardId) {

        $this->animalCubes->moveCard($cubeId, $hexId, 4);

        $this->notifyAllPlayers('materialMove', "", [
            'type' => MATERIAL_TYPE_CUBE,
            'from' => MATERIAL_LOCATION_CARD,
            'fromArg' => $fromCardId,
            'to' => MATERIAL_LOCATION_HEX,
            'toArg' => $this->getMostlyActivePlayerId(),
            'material' => [$this->getAnimalCubeFromDb($this->animalCubes->getCard($cubeId))],
        ]);
    }

    public function getCubesOnCard($cardId) {
        return array_keys($this->animalCubes->getCardsInLocation("card_" . $cardId));
    }

    public function getLastCubeOnCard($cardId) {
        $lastCube = $this->animalCubes->getCardOnTop("card_" . $cardId);
        //self::dump('******************lastCube*', $lastCube);
        return $lastCube ? $lastCube["id"] : null;
    }

    public function getAnimalCubesOnCards() {
        $sql = 'SELECT card_id id, card_type type, card_type_arg type_arg, card_location location, card_location_arg location_arg FROM animalCube where card_location like "card_%"';
        $tokens = $this->getAnimalCubesFromDb(self::getCollectionFromDb($sql));
        return $tokens;
    }

    public function getAnimalCubesOnPlayerBoard($playerId) {
        $sql = "SELECT card_id id, card_type type, card_type_arg type_arg, card_location location, card_location_arg location_arg FROM animalCube where card_location like '$playerId%'";
        $tokens = $this->getAnimalCubesFromDb(self::getCollectionFromDb($sql));
        $byCell = [];
        foreach (array_values($tokens) as $token) {
            if (!isset($byCell[$token->location])) {
                $byCell[$token->location] = [];
            }
            $byCell[$token->location][] = $token;
        }
        return $byCell;
    }
}
