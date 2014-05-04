<?php

/* --------------------------------------------------------------------*
 * ChangePassword.php                                                  *
 * --------------------------------------------------------------------*
 * Description - This class is used to change the password of a        *
 * forgoten user account. While its at it will also update the salt.   *
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

/*
 *+---------------------------------------------+
 *|             Command Outputs                 |
 *+-----------+---------------------------------+
 *| Response  | Description                     |
 *+-----------+---------------------------------+
 *|    -1     | Failed to complete somewhere.   |
 *|     1     | Succeeded at execution.         |
 *|     2     | Did not find the request.       |
 *+-----------+---------------------------------+
 */

class ChangePassword extends Command {

    //---------------------------------------------------------------//
    // Class Atributes                                               //
    //---------------------------------------------------------------//
        
    /* @var $dbAccess () The database access object linking to DB.   */
    private $dbAccess;
    
    /* @var $requestContent (Array) The content of the user request. */
    private $requestContent = array();

    //---------------------------------------------------------------//
    // Constructor/Destructors                                       //
    //---------------------------------------------------------------//

    /******************************************************************
     * @Description - Called to build the logout request command, It 
     * takes in the command parameters and saves them locally to the 
     * class.
     * 
     * @param $requestData - The json request data required to make the 
     * request.
     * 
     * @return None
     * 
     *****************************************************************/
    function __construct($requestData) {
        
        // Set the content locally.
        $this->requestContent = $requestData;
        
        // Create the new required database objects to preform task.
        $this->dbAccess = new AccountsDBTool ();

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
        $this->dbAccess = NULL;
        $this->requestContent = NULL;
        
    }

    //---------------------------------------------------------------//
    // Class Methods                                                 //
    //---------------------------------------------------------------//
    
    /* Executes the command defined for the service implementation. */
    public function executeCommand() {
                
        // --- Variable Declarations  -------------------------------//
        
        /* @var $commands (Array) Used to cross check the request.   */
        $commandParams = array ("securitycode","newpassword");
                
        /* @var $commandResult (Array) The result of the command.    */
        $commandResult = NULL;
                
        /* @var $result (object) The output of PDO sql executes.     */
        $result = NULL;
        
        /* @var $sqlQuery (object) The query to execute on service.  */
        $sqlQuery = NULL;

        // --- Main Routine ------------------------------------------//
        
        // Check if the request contains all necessary parameters.
        if ( isValidContent ($this->requestContent, $commandParams) ) {
        
            try {
                
                // Find the UserID so that we can change the password.
                $sqlQuery = "SELECT UserID FROM CodestormUsers.iForgots"
                    . " WHERE SecurityCode = :seccode LIMIT 1";
                $result = $this->dbAccess->executeFetch($sqlQuery,
                ["seccode",  $this->requestContent[securitycode]]);
                
                // Check if it exists if so change the password.
                if ($result["UserID"] != NULL) {
                    
                    // Generate the new salt.
                    $salt = keygen(24);
                    
                    // Update the password.
                    $sqlQuery = "UPDATE CodestormUsers.Accounts SET "
                        . "Password = : password, Salt = :salt WHERE "
                        . "UserID = :userid";
                    $result = $this->dbAccess->executeQuery ($sqlQuery,
                        ["password" => $this->requestContent["password"], 
                        "salt" => $salt, "userid" => $result["UserID"]]);
                    
                    // Check the result of the password change.
                    if ($result == true) {
                        $commandResult = ["response" => 1];
                    }
                    else {
                        $commandResult = ["response" => -1, "debug" => 
                            "IN CHANGEPASSWORD - SQL Failed."];
                    }     
                }
                
                else { // Did not find security code.
                    $commandResult = ["response" => 2];
                }
                
            } 
            
            catch (PDOException $pdoE) { // Error in request.
                $commandResult = ["response" => -1, "debug" => 
                    "IN ChangePassword -" + $pdoE->getMessage()];
            }
            
            // Return the result of the command.
            return $commandResult;
        }
        
    }
    
    //---------------------------------------------------------------//
    
}
