<?php

/* --------------------------------------------------------------------*
 * Logout.php                                                          *
 * --------------------------------------------------------------------*
 * Description - This class is used to preform code storm logout       *
 * services for users. This command connects to a database and         *
 * validates the logout is valid. If so it signs the user out.         *
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
 * NOTE : This only covers vital session removal task and doesn't 
 * take into account saving data. This for now will be a design 
 * decison that will force UpdateUserData anytime a change has 
 * happened to the users data. 
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

class Logout extends Command {
    
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
        
        /* @var $commands (Array) Used to cross check the request. */
        $commandParams = array ("userid", "sessionid");
        
        // Check if the request contains all necessary parameters.
        if ( isValidContent ($this->requestContent, $commandParams) ) {
            
            // Try to find the session.
            try {
                $sqlQuery = "DELETE FROM CodeStormUsers. "
                        . "Sessions WHERE UserID = :userID AND"
                        . " SessionID = :sessionID";
                
                $result = $this-> dbAccess->executeQuery($sqlQuery, 
                     ["userID"=> $this->requestContent["userid"], 
                     "sessionID" => $this->requestContent["sessionid"] ] );
                
                // Set the result.
                if ($result)
                    $commandResult = ["response" => 1];
                else
                    $commandResult = ["response" => -1];
            }
            
            catch (PDOException $pdoE) {
                $commandResult = ["response" => -1, "debug" =>
                    "IN Logout -" + $pdoE->getMessage()];
            }
            
            // Return the result of the command.
            return $commandResult;
            
        }
    }
    
    //---------------------------------------------------------------//

}
