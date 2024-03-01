<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Harmonies implementation : © Séverine Kamycki <mizutismask@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * harmonies.action.php
 *
 * Harmonies main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/harmonies/harmonies/myAction.html", ...)
 *
 */


class action_harmonies extends APP_GameAction {
    // Constructor: please do not modify
    public function __default() {
        if (self::isArg('notifwindow')) {
            $this->view = "common_notifwindow";
            $this->viewArgs['table'] = self::getArg("table", AT_posint, true);
        } else {
            $this->view = "harmonies_harmonies";
            self::trace("Complete reinitialization of board game");
        }
    }

    private function checkVersion() {
        $clientVersion = (int) self::getArg('version', AT_int, false);
        $this->game->checkVersion($clientVersion);
    }

    // TODO: defines your action entry points there
    /*
    
    Example:
  	
    public function myAction()
    {
        self::setAjaxMode();    
        self::checkVersion(); 

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $arg1 = self::getArg( "myArgument1", AT_posint, true );
        $arg2 = self::getArg( "myArgument2", AT_posint, true );

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->myAction( $arg1, $arg2 );

        self::ajaxResponse( );
    }
    
    */

    public function pass() {
        self::setAjaxMode();
        self::checkVersion();

        $this->game->pass();

        self::ajaxResponse();
    }
}
