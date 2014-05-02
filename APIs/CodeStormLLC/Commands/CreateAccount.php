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

//===================================================================//

/*
 *+---------------------------------------------+
 *|             Command Outputs                 |
 *+-----------+---------------------------------+
 *| Response  | Description                     |
 *+-----------+---------------------------------+
 *|    -1     | Failed to complete somewhere.   |
 *|     1     | Succeeded at execution.         |
 *|     2     | Account already exists.         |
 *+-----------+---------------------------------+
 */

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
    
    
    /******************************************************************
     * @Description - This method is used to create an account in the 
     * code storm accounts database via a traditional email account
     * creation. 
     * 
     * @param None
     * 
     * @return Returns the result of the creation request.
     * 
     *****************************************************************/
    private function defaultCreate () {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $commands (Array) Used to cross check the request. */
        $defaultParams = array ("newemail", "newpassword","deviceID");
        
        /* @var $sqlQuery (object) The query to execute on service. */
        $sqlQuery = NULL;
        
        /* @var $result (object) The output of PDO sql executes.  */
        $result = "";
        
        /* @var $newAccount (array) The new account data inserted.  */
        $newAccount = NULL;
        
        /* @var $salt (String) The new salt for the users password. */
        $salt = "";
        
        /* @var $defResult (Array) The result of this sub command. */
        $defResult = NULL;
        
        
        // --- Main Routine -----------------------------------------//
        
        // 1. Check that all parameters are there.
        if ( isValidContent ($this->requestContent, $defaultParams) )
        {
            try {
                // 2. Check that account doesn't already exist.
                $sqlQuery = "SELECT UserID FROM CodeStormUsers.Accounts WHERE "
                        . "Email = :email";
                
                $result = $this->dbAccess->executeFetch ($sqlQuery, 
                [ "email" => $this->requestContent["email"] ] );
                
                // 3. If the account doesn't exist create it.
                if ( $result ["ID"] == NULL ) {
                    
                    // 3A. Generate a salt key.
                    $salt = keyGen (24);

                    // 3B. Generate the password hash.
                    crypt($this->requestContent["newpassword"], '$2a$07$salt');
                    
                    // 3C. Create the request and add account.
                    $sqlQuery = "INSERT INTO CodeStormUsers.Accounts ( Email,"
                        . " Password, Username, Salt, deviceID) VALUES "
                        . "(:newemail, :newpassword, :newusername, :newsalt,"
                        . " :deviceID)";
                    
                    // 3D. Select Username from email. 
                    $newAccount = ["newemail" => $this->requestContent
                        ["newemail"], "newpassword" => $this->
                        requestContent["newpassword"], "newsalt" => $salt, 
                        "deviceid" => $this->requestContent["deviceid"], 
                        "newusername",substr($this->requestContent["newemail"],
                        0, strpos($this->requestContent["newemail"], "@"))];
                    
                    // 3E. submit request. 
                    $result = $this->dbAccess->executeQuery 
                            ($sqlQuery, $newAccount);

                    // 4. Return the result of the creation.
                    if ($result == true) {
                        $defResult = ["response" => 1];
                    }
                    
                    else {
                        $defResult = ["response" => -1, "debug" => 
                            "In CreateAccount (Default) - The create request"
                            . " Failed."];
                    }
                }
                
                else { // Account already exists.
                    $defResult = ["response" => 2];
                }
            }
            
            catch (PDOException $pdoE) { // Error in connection.
                $defResult =["response" => -1, "debug" => "In CreateAccount"
                    . "(Default) - "+$pdoE->getMessage()];
            }
        }
        
        else { // Invalid data types.
            $defResult = ["response" => -1,"debug"=> "In CreateAccount"
                . " (Default) - parameter missmatch."];
        }              
        
        // Return the end result.
        return $defResult;
        
    }
    
    
    /* Executes the command defined for the service implementation. */
    public function executeCommand() {
        
        // Make sure that there is a create type parameter.
        if ( isValidContent ($this->requestContent, ["createtype"]) ) {
         
            // Create an account depending on credentials...
            switch ($this->requestContent["createtype"]) {
                case 0: // Default email & password.
                    $commandResult = defaultCreate ();
                    break;
                
                case 1: // Facebook creation.
                    $commandResult = facebookCreate();                    
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

    
    /******************************************************************
     * @Description - This method is used to create an account in the 
     * code storm accounts database via a facebook SSO. 
     * 
     * @param None
     * 
     * @return Returns the result of the creation request.
     * 
     *****************************************************************/
    private function facebookCreate () {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $fbParams (Array) Used to cross check the request. */
        $fbParams = array ("newemail", "fbid", "newusername", 
            "deviceID");
        
        /* @var $sqlQuery (object) The query to execute on service. */
        $sqlQuery = NULL;
        
        /* @var $result (object) The output of PDO sql executes.  */
        $result = "";
        
        /* @var $newAccount (array) The new account data inserted.  */
        $newAccount = NULL;
        
        /* @var $salt (String) The new salt for the users password. */
        $salt = "";
        
        /* @var $fbResult (Array) The result of this sub command. */
        $fbResult = NULL;
        
        // --- Main Routine ------------------------------------------//
        
        // Make sure that there is a create type parameter.
        if ( isValidContent ($this->requestContent, $fbParams) ) {
            
            try {
                // 1. Validate the account doesn't already exist.
                $sqlQuery = "SELECT UserID FROM CodeStormUsers.Accounts WHERE "
                        . "FacebookID = :fbid OR Email = :email  LIMIT 1";
                $result = $this->dbAccess->executeFetch ($sqlQuery, 
                        ["fbid" => $this->requestContent["fbid"], "email"
                            => $this->requestContent["newemail"] ] );

                // 2. Create the account.
                if ($result ["FacebookID"] == NULL) {

                    // 2A. Generate a salt key.
                    $salt = keyGen (24);

                    // 2B. Generate the password hash.
                    crypt($this->requestContent["fbid"], '$2a$07$salt');

                    // 2C. Create the account.
                    $sqlQuery = "INSERT INTO CodeStormUsers.Accounts ( Email, "
                        . "  Password, Salt, FacebookID, AccountType, Username,"
                        . " DeviceID) VALUES (:newemail, :newpassword, "
                        . " :newsalt, :facebookid, 'FACEBOOK', :fbusername,"
                        . " :deviceid)";

                    $newAccount = ["newemail" => $this->requestContent
                        ["newemail"], "newpassword" => $this->requestContent
                        ["newpassword"], "newsalt" => $salt, "facebookid"=> 
                        $this-> requestContent["fbid"], "fbusername" => 
                        $this-> requestContent["newusername"], "deviceid" =>
                        $this->requestContent["deviceid"]];

                    // Run create query.
                    $result = $this->dbAccess->executeQuery 
                            ($sqlQuery, $newAccount);

                    // 2D. Return the result of the creation.
                    if ($result == true) {
                        $fbResult = ["response" => 1];
                    }
                    else {
                        $fbResult = ["response" => -1, "debug" =>
                            "In CreateAccount (Facebook) - The create request "
                            . "Failed."];
                    }
                }

                else { // Account already exists.
                    $fbResult = ["response" => 2];
                }

            }
            
            catch (PDOException $pdoE) {
                $fbResult = ["response" => -1, "debug" => "In CreateAccount"
                    . "(Facebook) - "+$pdoE->getMessage()];
            }
        }
        
        else {
            $fbResult = ["response" => -1,"debug"=> "In CreateAccount"
                . " (Facebook) - parameter missmatch."];
        }
        
        // Return the end result.
        return $fbResult;
            
    }
    
    //---------------------------------------------------------------//

}
