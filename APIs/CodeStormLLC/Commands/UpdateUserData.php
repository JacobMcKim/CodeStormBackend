<?php

/* --------------------------------------------------------------------*
 * UpdateUserData.php                                                  *
 * --------------------------------------------------------------------*
 * Description - This class is used to update user specific data about *
 * their account with Code Storm LLC. Certain atributes will be        *
 * enabled to be updated in the database and this class interfaces     *
 * those abilities.                                                    *
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
 * TODO - USERNAME NEEDS TO BE CHECKED BEFORE INSERTED. IF SOMEONE ALREADY
 * USES NAME THEN WE HAVE TO FAIL THE REQUEST.
 */

/*
 *+---------------------------------------------+
 *|             Command Outputs                 |
 *+-----------+---------------------------------+
 *| Response  | Description                     |
 *+-----------+---------------------------------+
 *|    -1     | Failed to complete somewhere.   |
 *|     1     | Succeeded at execution.         |
 *+-----------+---------------------------------+
 */

class UpdateUserData Extends Command {
    
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
    * @Description - This method is called to check and see if a 
    * desired username already exists in the database.
    * 
    * @param $Username (String) - The username to be searched for.  
    * 
    * @return Whether or not the username exists (Boolean).
    * 
    *****************************************************************/
    private function checkUsername ($Username) {
        
        // --- Variable Declarations  -------------------------------//

        /* @var $result (Array) The result of the query search.      */
        $result = NULL;
                
        /* @var $userExist (Boolean) The result of this method.      */
        $userExist = False;
        
        /* @var $sql (String) The query for the username tied.       */
        $sql = "SELECT UserID FROM CodestormUsers.Accounts WHERE"
                . " Username = :username LIMIT 1";
        
        // --- Main Routine -----------------------------------------//

        // Run query to find user.
        $result = $this->dbAccess->executeFetch ($sql,$Username);
        
        // Determine if the user exists or not and return.
        if ($result ["UserID"] != NULL) {
            $userExist = true;
        }
        else {
            $userExist = false;
        }
 
        return $userExist;
        
    }
    
    
    /* Executes the command defined for the service implementation. */
    public function executeCommand() {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $commands (Array) Used to cross check the request.    */
        $commandParams = array ("userid", "sessionid", "updates");
                
        /* @var $commandResult (Array) the result of this command.   */
        $commandResult = NULL;
        
        /* @var $key (String) An element to update given in array.   */
        $key = "";
        
        /* @var $keys (Array) List of element names to update in db. */
        $keys = NULL;
        
        /* @var $updates (Array) List of elements to update in db.   */
        $updates = NULL;
                
        /* @var $updateSql (String) the SQL update command.          */
        $updateSql = "UPDATE CodestormUsers.Accounts SET ";

        /* @var $result (Array) The result of the items updated.     */
        $result = NULL;
        
        // --- Main Routine -----------------------------------------//
        
        // Validate the request parameters and that a session exists.
        if ( isValidContent ($this->requestContent, $commandParams)  &&
            checkSession ($this->dbAccess, $this->requestContent["userid"],
            $this->requestContent["sessionid"]) ) {
            
            // Decided what to push an update to.
            $updates = $this->requestContent["updates"];
            $keys = array_keys($updates);
            
            // Run the command and check the results. (Catch checkUsername)
            try {

                foreach ($key as $keys) {

                    // Build sql.
                    switch ($key) {

                        case "coins" :
                            $updateSql .= "Coins = :coins";
                            break;

                        case "username" :
                            if (!$this->checkUsername ($updates["username"])) {
                                $updateSql .= "Username = :username";
                            }
                            else { // Username already exists.
                                $commandResult = ["response" => 2];
                            }
                            break;

                        default : // Dont append anything the table.
                            break;
                    }

                    $updateSql.=", ";
                }

                // Make sure the username doesn't exist.
                if ($commandResult == NULL) {

                    // Remove the last comma in the string and  end the value.
                    rtrim($updateSql, ',');
                    $updateSql.= "WHERE UserID = :userid";

                    // Add the user id to the updates list.
                    if ($updates["userid"] != NULL) {
                        unset($updates["userid"]); 
                    }

                    $updates["userid"] = $this->requestContent["userid"];


                    $result = $this->dbAccess->executeQuery($updateSql, 
                            $updates);

                    // Check the result of the command execution.
                    if ($result == True) {
                        $commandResult = ["response" => 1];
                    }
                    else {
                        $commandResult = ["response" => -1];
                    }
                }
            } 
            
            catch (Exception $pdoE) { // Error in request.
                $commandResult = ["response" => -1, "debug" => 
                    "IN UPDATEUSERDATA - " + $pdoE->getMessage()];
            }
        }
        
        else { // Invalid arguments.
            $commandResult = ["response" => -1, "debug" => 
                "IN UPDATEUSERDATA - command invalid." ];
        }
        
        // Return the result of this commands execution.
        return $commandResult;
        
    }
 
    //---------------------------------------------------------------//
    
}
