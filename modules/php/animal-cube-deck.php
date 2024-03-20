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
        $this->animalCubes->createCards($tokens, 'deck');
    }

    /**
     * Pick tokens to fill an animal card.
     */
    public function fillAnimalCard(AnimalCard $card) {
        $cubes = [];
        for ($i = 0; $i < count($card->pointLocations); $i++) {
            $cubes[] = $this->animalCubes->pickCardForLocation("deck", "card_" . $card->id, $i);
        }
        $this->notifyAllPlayers('materialMove', "", [
            'type' => MATERIAL_TYPE_CUBE,
            'from' => MATERIAL_LOCATION_DECK,
            'to' => MATERIAL_LOCATION_CARD,
            'toArg' => $card->id,
            'material' => $this->getAnimalCubesFromDb($cubes),
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
        self::dump('******************lastCube*', $lastCube);
        return $lastCube ? array_pop($lastCube) : null;
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
