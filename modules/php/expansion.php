<?php

trait ExpansionTrait {

    function getExpansion() {
        return $this->isSpiritCardsOn() ? SPIRITS : EXPANSION;
    }
    /**
     * List the colored tokens that will be used for the game.
     */
    function getColoredTokensToGenerate() {
        $cards = [];

        switch ($this->getExpansion()) {
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
        $selectedCards = [];
        $cards = [];

        switch ($this->getExpansion()) {
            case 0:
                $selectedCards = array_filter($this->ANIMAL_CARDS[1], fn ($c) => $c->isSpirit === false);
                break;

            default:
                $selectedCards = $this->ANIMAL_CARDS[1];
                break;
        }
        foreach ($selectedCards as $typeArg => $card) {
            $cards[] = ['type' => 1, 'type_arg' => $typeArg, 'nbr' => 1];
        }

        return $cards;
    }

    /**
     * Return the number of spirit cards shown at the beginning.
     */
    function getInitialSpiritCardNumber(): int {
        //$playerCount = $this->getPlayerCount();
        switch ($this->getExpansion()) {
            default:
                return 2;
        }
    }
}
