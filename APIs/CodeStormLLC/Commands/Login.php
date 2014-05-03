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

/*
 *+---------------------------------------------+
 *|             Command Outputs                 |
 *+-----------+---------------------------------+
 *| Response  | Description                     |
 *+-----------+---------------------------------+
 *|    -1     | Failed to complete somewhere.   |
 *|     1     | Succeeded at execution.         |
 *|     2     | Invalid login credentials.      |
 *|     3     | Session exists somewhere else.  |
 *+-----------+---------------------------------+
 */

class Login extends command {
    
    //---------------------------------------------------------------//
    // Class Atributes                                               //
    //---------------------------------------------------------------//
    
    /* @var $requestContent (Array) The content of the user request. */
    private $requestContent = array();
    
    /* @var $dbAccess () The database access object linking to DB.   */
    private $dbAccess;
    
    //---------------------------------------------------------------//
    // Constructor/Destructors                                       //
    //---------------------------------------------------------------//

    /******************************************************************
     * @Description - Called to build the login request command, It 
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
    
    
    /******************************************************************
     * @Description - This method is used to generate and add sessions
     * for user logins.
     * 
     * @param None
     * 
     * @return The session ID if successful otherwise it will return 
     * the response and a debugparameter upon failure (associative 
     * array).
     * 
     *****************************************************************/
    private function createSession () {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $sqlQuery (object) The query to execute on service. */
        $sqlQuery = "";
        
        /* @var $result (object) The output of PDO sql executes.    */
        $result = NULL;
        
        /* @var $sessionID (String) The id of the new session.       */
        $sessionID = keyGen (16);
        
        // --- Main Routine ------------------------------------------//

        // Create the query.
        $sqlQuery = "INSERT INTO CodeStormUsers.Sessions (UserID, "
                . "SessionID, ExpireTime) VALUES (:userid, :sessionid, "
                . "TIMESTAMPADD(MINUTE,25,CURRENT_TIMESTAMP))";
        
        // Attempt to create the new session.
        try {
            $result = $this->dbAccess->executeQuery ($sqlQuery, 
                ["userid" => $this->requestContent["userid"],
                "sessionid" => $sessionID]);
        
            // Return the outcome of the session creation.
            $result = $result == true ? ["sessionID" => $sessionID] :
                ["response" => -1, "debug" => "IN LOGIN (CreateSession()): "
                . "Session Creation failed."];
        } 
        
        catch (PDOException $pdoE) { // Error occured in creating session.
            $result = ["response" => -1, "debug" => 
                "IN LOGIN (CreateSession()) - " + $pdoE->getMessage()];
        }
        
        return $result;
        
    }
    
    
    /* Executes the command defined for the service implementation. */
    public function executeCommand() {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $commands (Array) Used to cross check the request.   */
        $commandParams = array ("email", "password", "signType");
        
        /* @var $commandResult (Array) the result of this command.   */
        $commandResult = NULL;
        
        /* @var $findResult (Array) The result of the account search. */
        $findResult = NULL;
        
        /* @var $sessionResult (Array) The result of the session search. */
        $sessionResult = NULL;
        
        // --- Main Routine ------------------------------------------//
        
        // Check if the request contains all necessary parameters.
        if ( isValidContent ($this->requestContent, $commandParams) ) {
                        
            // Find the account data tied to the email.
            $findResult = $this->findAccount ();
            
            // Check for session data next if passwords match.
            if ($findResult != NULL && crypt ($this->requestContent["password"],
                '$2a$07$findResult["Password"]') == $findResult["Password"]) {
                
                // Check that a session isn't already going.
                if ( !checkSession ($findResult["UserID"]) ) {
                    
                    // Create a new session for the user.
                    $sessionResult = $this->createSession ();

                    // Get account data and return the result.
                    if ($sessionResult ["sessionID"] != NULL)
                    {
                        $commandResult = $this->getAccountData (
                                $findResult["UserID"] );
                    }

                    else if ($sessionResult["response"] == -1) {
                        $commandResult = $sessionResult;
                    }

                    else { 
                        $commandResult = ["response" => -1];
                    }                    
                }
                
                else { // Session already exists somewhere.
                    $commandResult = ["response" => 3];
                } 
            }
            
            else { // Account not found/Invalid.
                $commandResult = ["response" => 2];
            }
        }
        
        else {
            $commandResult = ["response"=> -1, "debug" => 
                            "ERROR IN LOGIN - command invalid."];
        }
        
        // Return the result of this commands execution.
        return $commandResult;
        
    }
    
    
    /******************************************************************
     * @Description - This method is used to look for a code storm 
     * account linked in the database by an email given by the user.
     * 
     * @param None
     * 
     * @return Returns the elements found by the search of the email.
     * 
     *****************************************************************/
    private function findAccount () {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $sqlQuery (object) The query to execute on service.  */
        $sqlQuery = NULL;
        
        /* @var $result (object) The output of PDO sql executes.     */
        $result = NULL;
        
        // --- Main Routine ------------------------------------------//
        
        // Preform Data request for account.
        $sqlQuery = "SELECT * FROM Accounts WHERE Email = :email"
                . " LIMIT 1";
        $result = $this->dbAccess->executeFetch( $sqlQuery,
            ["email",$this->requestContent["email"]] );
        
        // Return the result of the search.
        return $result;
        
    }
    
    
    /******************************************************************
     * @Description - This method is used to pull account data of a
     * code storm user given a userID.
     * 
     * @param $userID - The identification number of the user were 
     * looking to pull data on (Integer).
     * 
     * @return Returns the elements found by the search of the email.
     * 
     *****************************************************************/
    private function getAccountData ($userID) {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $sqlQuery (object) The query to execute on service.  */
        $sqlQuery = NULL;
        
        /* @var $result (object) The output of PDO sql executes.     */
        $result = NULL; 
        
        // --- Main Routine ------------------------------------------//
        
        // Attempt to pull data from server.
        try {
            $sqlQuery = "SELECT UserID, Username, Coins FROM "
                 . "CodeStormUsers.Accounts WHERE UserID = :userid";
            $result = $this->dbAccess->executeFetch( $sqlQuery,
                ["userid",$userID] );
        } 
        
        catch (PDOException $pdoE) { // Error in request.
            $result = ["response" => -1, "debug" => 
                "IN LOGIN (CreateSession()) - " + $pdoE->getMessage()];
        }
        
        // Return the result of the search.
        return $result;
        
    }
    
    //---------------------------------------------------------------//

}
