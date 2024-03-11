<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Harmonies implementation : © Séverine Kamycki <mizutismask@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * harmonies.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in harmonies_harmonies.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */

require_once(APP_BASE_PATH . "view/common/game.view.php");

class view_harmonies_harmonies extends game_view {
  function getGameName() {
    return "harmonies";
  }
  function build_page($viewArgs) {
    // Get players & players number
    $players = $this->game->loadPlayersBasicInfos();
    $players_nbr = count($players);

    /*********** Place your code below:  ************/
    // Create the board
   /* $this->page->begin_block('harmonies_harmonies', 'cell');
    foreach ($this->game->getHexesCoordinates() as $hex) {
      $col = $hex["col"];
      $row = $hex["row"];
      $this->page->insert_block('cell', [
        'I' => $col,
        'J' => $row,
      ]);
    }*/
    /*********** Do not change anything below this line  ************/
  }
}
