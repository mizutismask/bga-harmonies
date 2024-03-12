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
        for ($i = 0; $i < count($card->pointLocations); $i++) {
            $this->animalCubes->pickCardForLocation("deck", "card_" . $card->id, $i);
            $this->notifyAllPlayers('animalCubeMove', "", [
                'cardId' => $card->id,
                'spot' => $i,
            ]);
        }
    }

    public function getCubesOnCard($cardId) {
        return array_keys($this->animalCubes->getCardsInLocation("card_" . $cardId));
    }

    public function getLastCubeOnCard($cardId) {
        $lastCube = array_keys($this->animalCubes->getCardOnTop("card_" . $cardId));
        return $lastCube ? array_pop($lastCube) : null;
    }

    public function getAnimalCubesOnCards() {
        $sql = 'SELECT card_id id, card_type type, card_type_arg type_arg, card_location location, card_location_arg location_arg FROM animalCube where card_location like "card_%"';
        $tokens = $this->getAnimalCubesFromDb(self::getCollectionFromDb($sql));
        return $tokens;
    }

    public function getAnimalCubesOnPlayerBoards() {
        $sql = "SELECT card_id id, card_type type, card_type_arg type_arg, card_location location, card_location_arg location_arg FROM animalCube where card_location like 'hex_%'";
        $tokens =  $this->getAnimalCubesFromDb(self::getCollectionFromDb($sql));
        return $tokens;
    }
}
