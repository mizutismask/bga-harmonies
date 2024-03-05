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
            $this->animalCubes->pickCardForLocation("deck", $card->id, $i);
            $this->notifyAllPlayers('animalCubeMove', "", [
                'cardId' => $card->id,
                'spot' => $i,
            ]);
        }
    }
}
