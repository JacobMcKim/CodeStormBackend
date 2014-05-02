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
    
    //---------------------------------------------------------------//
    // Absract Methods                                               //
    //---------------------------------------------------------------//

    /* Executes the command defined for the service implementation. */
    abstract public function executeCommand ();
    
    //---------------------------------------------------------------//
    // Concrete Class Methods                                        //
    //---------------------------------------------------------------//
    
    /* Validates the commands parameters before execution. */
    protected function isValidContent( $Content, $arrayParams ) {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $isValid Boolean The result if all values are there. */
        $isValid = true; 
        
        /* @var $isValid String The current parameter being checked. */
        $param = NULL;
        
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
    
    
    /******************************************************************
     * @Description - This method is used to validate whether a 
     * session for a given account actively exists in the sessions 
     * database.
     * 
     * @param $dbRequest - The IDatabaseTool used to communicate with
     * the database (IDatabaseTool).
     * 
     * @param $userID - The user id to be used in the session search.
     * 
     * @param $sessionID The session id used as a password to make sure
     * were talking to the right session.
     * 
     * @return A boolean value depending on whether a session was found
     * matching the given criteria. (True - Yes / False - No)
     * 
     * @Throws PDOException - If something goes wrong with the fetch
     * command this will throw this. It should be handled in the class
     * calling this method.
     * 
     *****************************************************************/
    protected function checkSession ($dbRequest, $userID, $sessionID) {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $sqlQuery (object) The query to execute on service. */
        $sqlQuery = "";
        
        /* @var $result (object) The output of PDO sql executes.  */
        $result = NULL;
        
        /* @var $commandResult (boolean) the result of this command. */
        $commandResult = false;
        
        // --- Main Routine ------------------------------------------//

        if ($userID != NULL) {
            
            // Search for any sessions that exist.
            $sqlQuery = "SELECT UserID FROM CodeStormUsers.Sessions "
                . "WHERE UserID = :userid AND SessionID = :sessionid "
                . "AND ExpireTime > CURRENT_TIMESTAMP";
            $result = $dbRequest->executeFetch($sqlQuery, 
                ["userid" => $userID, "sessionid" => $sessionID]);

            // Determine if we found the session were looking for.
            if ($commandResult["UserID"] != NULL) {
                $commandResult = true;
            }
        }
        
        // Return the result of the execution.
        return $commandResult;
        
    }
    
    //---------------------------------------------------------------//
    
}
