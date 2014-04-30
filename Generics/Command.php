<?php

/* --------------------------------------------------------------------*
 * Command.php                                                         *
 * --------------------------------------------------------------------*
 * Description - This abstract class implements the ICommand interface *
 * to lay the foundational elements for all command classes that are   *
 * derived from it. It contains a generic isValidContent method that   *
 * can check if all reqired parameters exist. It can be overriden.     *
 * --------------------------------------------------------------------*
 * Project: Code Storm Backend 1.0.01                                  *
 * Author : McKim A. Jacob                                             *
 * Date Of Creation: 4 - 29 - 2014                                     *
 * --------------------------------------------------------------------*
 * Copyright © 2014 Code Storm LLC. All Rights Reserved.               *
 * --------------------------------------------------------------------*
 * NOTICE:  All information contained herein is, and remains           *
 * the property of Code Storm LLC. and its suppliers, if any. The      *
 * intellectual and technical concepts contained herein are            *
 * proprietary to Code Storm LLC. and its suppliers and may be covered *
 * by U.S. and Foreign Patents, patents in process, and are protected  *
 * by trade secret or copyright law. Dissemination of this information *
 * or reproduction of this material is strictly forbidden unless prior *
 * written permission is obtained from Code Storm LLC.                 *
 * Thank you.                                                          *
 * ------------------------------------------------------------------- */

//===================================================================//
//  NOTES & BUGS AS OF 4-29-2014                                     //
//===================================================================//

/*
 *
 */

//===================================================================//
//  Includes                                                         //
//===================================================================//

abstract class Command implements ICommand {

    /* Executes the command defined for the service implementation. */
    abstract public function executeCommand ();
    
    /* Validates the commands parameters before execution. */
    protected function isValidContent( $Content, $arrayParams ) {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $isValid Boolean The result if all values are there. */
        $isValid = true; 
        
        /* @var $isValid String The current parameter being checked. */
        $param;
        
        // --- Main Routine -----------------------------------------//
        
        // Check each parameter and insure they are there.
        foreach ( $param as $arrayParams )
        {
            if ( $Content [$param] == NULL ) {
                $isValid = false;
                break;
            }
        }
        
        return $isValid;
       
    }
    
}
