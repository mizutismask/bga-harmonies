<?php

trait ExpansionTrait {

    /**
     * List the colored tokens that will be used for the game.
     */
    function getColoredTokensToGenerate() {
        $cards = [];
        $expansion = EXPANSION;

        switch ($expansion) {
            default:
                $cards = array(
                    array('type' => 1, 'type_arg' => BLUE, 'nbr' => 23),
                    array('type' => 1, 'type_arg' => GRAY, 'nbr' => 23),
                    array('type' => 1, 'type_arg' => BROWN, 'nbr' => 21),
                    array('type' => 1, 'type_arg' => GREEN, 'nbr' => 19),
                    array('type' => 1, 'type_arg' => YELLOW, 'nbr' => 19),
                    array('type' => 1, 'type_arg' => RED, 'nbr' => 15),
                );
                break;
        }

        return $cards;
    }

    /**
     * List the animal cards that will be used for the game.
     */
    function getAnimalCardsToGenerate() {
        $cards = [];
        $expansion = EXPANSION;

        switch ($expansion) {
            default:
                foreach ($this->ANIMAL_CARDS[1] as $typeArg => $card) {
                    $cards[] = ['type' => 1, 'type_arg' => $typeArg, 'nbr' => 1];
                }
                break;
        }

        return $cards;
    }

    /**
     * Return the number of ANIMAL_CARDS cards shown at the beginning.
     */
    function getInitialDestinationCardNumber(): int {
        $playerCount = $this->getPlayerCount();
        switch (EXPANSION) {
            default:
                if ($playerCount == 2 || $playerCount == 3)
                    return 12;
                return 9;
        }
    }

    /**
     * Return the minimum number of ANIMAL_CARDS cards to keep at the beginning.
     */
    function getInitialDestinationMinimumKept() {
        switch (EXPANSION) {
            default:
                return 2;
        }
    }

    /**
     * Return the number of ANIMAL_CARDS cards shown at pick destination action.
     */
    function getAdditionalDestinationCardNumber() {
        switch (EXPANSION) {
            default:
                return 2;
        }
    }
}
