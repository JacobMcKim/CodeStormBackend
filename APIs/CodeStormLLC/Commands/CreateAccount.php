<?php

/* --------------------------------------------------------------------*
 * CreateAccount.php                                                   *
 * --------------------------------------------------------------------*
 * Description - This class is used to create new Code Storm user      *
 * accounts on the Code Storm service API. These accounts can then be  *
 * used to access any of Code Storm llcs products and platforms.       *
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

class CreateAccount extends Command {

    //---------------------------------------------------------------//
    // Class Atributes                                               //
    //---------------------------------------------------------------//
    
    /* @var $requestContent (Array) The content of the user request. */
    private $requestContent = array();
    
    /* @var $dbAccess () The database access object linking to DB.  */
    private $dbAccess;
    
    //---------------------------------------------------------------//
    // Constructor/Destructors                                       //
    //---------------------------------------------------------------//

    /******************************************************************
     * @Description - Called to build the create account command, It 
     * takes in an newemail, password and optionally a user name and 
     * creates an account in code storms user base.
     * 
     * @param $requestData - The json request data required to make the 
     * request.
     * 
     * @return None
     * 
     *****************************************************************/
    function __construct($requestData) {
        
        // Set the content locally.
        $requestContent = $requestData;
        
        // Create the new required database objects to preform task.
        $dbAccess = new CodeStormAccountsDB ();
        CodeStormAccountsDB -> selectTable ("Accounts");
        
    }
    
    /******************************************************************
     * @Description - Called when the command has finished executing
     * and its time to tear down all the command's resources.
     * 
     * @param None 
     * 
     * @return None
     * 
     *****************************************************************/   
    function __destruct() {
        $dbRequester = NULL;
        $requestContent = NULL;
        
    }

    //---------------------------------------------------------------//
    // Class Methods                                                 //
    //---------------------------------------------------------------//
    
    /* Executes the command defined for the service implementation. */
    public function executeCommand() {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $commands (Array) Used to cross check the request. */
        $commands = array ("newemail", "newpassword", "createtype");
        
        // --- Main Routine ------------------------------------------//
        
        // Check if the request contains all necessary parameters.
        if ( isValidContent ($requestContent, $commands) ) {
         
            // Create an account depending on credentials...
            switch ($createtype) {
                case 0: // Default email & password.
                    
                    break;
                
                case 1: // Facebook creation.
                    
                    break;
                
                default:
                    $commandResult = ["response"=> -1, "debug" => 
                        "ERROR IN CREATE ACCOUNT - invalid create type."];
                    break;
                
            }
            
        }
        
        else { // Command not valid.
            $commandResult = ["response"=> -1, "debug" => 
                    "ERROR IN CREATE ACCOUNT - command invalid."];
        }
        
        // Return the result of this commands execution.
        return $commandResult;
        
    }
    
    //---------------------------------------------------------------//

}
