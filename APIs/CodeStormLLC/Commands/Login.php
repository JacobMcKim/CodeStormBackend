<?php

/* --------------------------------------------------------------------*
 * Login.php                                                           *
 * --------------------------------------------------------------------*
 * Description - This class is used to preform code storm login        *
 * services for users. This command connects to a database and         *
 * validates the login is valid. If so it signs the user in.           *
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
include 'GetUserData.php';

//===================================================================//

/*
 *+---------------------------------------------+
 *|             Command Outputs                 |
 *+-----------+---------------------------------+
 *| Response  | Description                     |
 *+-----------+---------------------------------+
 *|    -1     | Failed to complete somewhere.   |
 *|     1     | Succeeded at execution.         |
 *      2     | Invalid login credentials.      |
 *|     3     | Session exists somewhere else.  |
 *+-----------+---------------------------------+
 */

class Login extends command {
    
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
     * @Description - Called to build the login request command, It 
     * takes 
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
        $commands = array ("email", "password", "deviceID", "signType");
        
        /* @var $getInfoCommand (Command) Called to get login data. */
        $getInfoCommand = NULL;
        
        /* @var $accountInfo (Array) JSON result of $getInfoCommand. */
        $accountInfo = array();
        
        /* @var $AccountInfoRequest (Array) the result of execution. */
        $AccountInfoRequest = array ();
        
        /* @var $commandResult (Array) the result of this command.   */
        $commandResult = array ();
        
        // --- Main Routine ------------------------------------------//
        
        // Check if the request contains all necessary parameters.
        if ( isValidContent ($requestContent, $commands) ) {
            
            // Connect to DB check login, password, and session.
            //TODO : Build this out.
            
            // If a session doesn't exist and login correct sign in.
            if ()
            {   
                // Create a new session, request for account info.
                $getInfoCommand = GetUserData ();
                $accountInfo = $getInfoCommand -> executeCommand 
                       ($AccountInfoRequest);
                
                // Check and make sure the request worked.
                if ($accountInfo ["response"] != NULL && 
                        $accountInfo ["response"] == 1) {
                    $commandResult = $accountInfo;
                }
                else {
                    $commandResult = ["response" => -1, "debug" => 
                            "ERROR IN LOGIN - GetInfoFailed."];
                }
            }
            
            // Say session already exists.
            else {
                $commandResult = ["response"=> 3];
            }
        }
        
        else {
            $commandResult = ["response"=> -1, "debug" => 
                            "ERROR IN LOGIN - command invalid."];
        }
        
        // Return the result of this commands execution.
        return $commandResult;
        
    }
    
    //---------------------------------------------------------------//

}
