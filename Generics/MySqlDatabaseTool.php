<?php

/* --------------------------------------------------------------------*
 * MySqlDataBaseTool.php                                               *
 * --------------------------------------------------------------------*
 * Description - This class is used as a default parent class for any  *
 * MySQL based DB Tools to use as their foundation. It implements all  *
 * methods defined in the IDatabaseTool inteface and is out of the box *
 * ready to go for tayloring to specific Connections.                  *
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
 * TODO: ADD debug to constructor & open/close. 4/30/14
 * TODO: ADD a session check method. 5/1/14.
 */

//===================================================================//
//  Includes                                                         //
//===================================================================//

class MySqlDatabaseTool Implements IDatabaseTool {

    //---------------------------------------------------------------//
    // Class Atributes                                               //
    //---------------------------------------------------------------//
        
    /* The host address of the database service. */
    const _host = "127.00.00.01:3306";
    
    /* The account username to use when signing into the service. */
    const _user = "root";
    
    /* The account password to use when signing into the service. */
    const _password = "root";
    
    /* @var $isOpen (Boolean) Whether or not the connection is open. */
    private $isOpen = false;
    
    /* @var $dbConnect (PDOObject) The connection to the DB service. */
    private $dbConnect = NULL;
    
    //---------------------------------------------------------------//
    // Constructor/Destructors                                       //
    //---------------------------------------------------------------//

    /******************************************************************
     * @Description - Called to generate a MySqlDatabase Tool. 
     * 
     * @param $requestData - The json request data required to make the 
     * request.
     * 
     * @return None
     * 
     *****************************************************************/
    function __construct() {
    
        // Attempt to open the connection to the database.
        if ( !openConnection () ) {
            throw new Exception ("AccountsDBTool (Constructor): "
                    . "Failed to connect.");
        }
        
        else {
            $this->isOpen = true;
        }
        
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
        
        // Attempt to open the connection to the database.
        if ( !closeConnection () ) {
            throw new Exception ("AccountsDBTool (Destructor): "
                    . "Failed to close connection.");
        }
        
        else {
            $this->isOpen = false;
        }
        
    }

    //---------------------------------------------------------------//
    // Class Methods                                                 //
    //---------------------------------------------------------------//
    
    /* closes the connection to the DB service. */ 
    private function closeConnection() {
        
        // --- Variable Declarations  -------------------------------//

        /* @var $success (boolean) The success of openConnection. */
        $success = true;
        
        // --- Main Routine -----------------------------------------//
        
        // If the database is open close it.
        if ($this->isOpen) {
            try {
                $this->dbConnect = NULL;
            }
            catch (PDOException $pd) {
                $success = false;
                //TODO : ADD DEBUG
            }
        }
        
        // Return the execution result.
        return $success;
        
    }
    
    /* Execute a sql command to the database.
     * NOTE: The delimiter for this command is ':'.*/
    public function executeQuery ($RequestString, $RequestAtributes) {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $query - The command to be executed by PDO. */
        $query = NULL;
    
        // --- Main Routine -----------------------------------------//
        
        // Make sure that the function.
        if ($RequestString != null && mb_substr_count 
                ($RequestAtributes, ':') == count ($RequestAtributes) )
        {
            // Execute the command.
            $query = $this->dbConnect->prepare($RequestString, 
                    array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            return $query-> execute ($RequestAtributes);
        }
        
        else {
            throw new PDOException ("In AccountsDBTool(ExecuteCommand)"
                    . " - command string and parameter mismatch.");
        }
        
    }
    
    /* Issues a query to the DB service as well fetches results. 
     * NOTE: The delimiter for this command is ':'.*/
    public function executeFetch ($RequestString, $RequestAtributes) {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $results The output of the executed command. */
        $results = NULL;
        
        // --- Main Routine -----------------------------------------//
        
        // Make sure that the function.
        if ($RequestString != null && mb_substr_count 
                ($RequestAtributes, ':') == count ($RequestAtributes) )
        {
            // Execute the command.
            $query = $this->dbConnect->prepare($RequestString, 
                    array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $results = $query-> execute ($RequestAtributes);
            
            // Fetch results and return.
            return $results->fetch(PDO::FETCH_ASSOC);    
        }
        
        else {
            throw new PDOException ("In AccountsDBTool(ExecuteCommand)"
                    . " - command string and parameter mismatch.");
        }
        
    }
    
    /******************************************************************
     * @Description - An accessor method stating whether or not the 
     * connection is open to the database or not.
     * 
     * @param None 
     * 
     * @return Whether or not a connection exist (Boolean).
     * 
     *****************************************************************/ 
    public function getIsOpen () {
        return $this->isOpen;
        
    }
    
    /* Opens a connection to the DB service. */
    private function openConnection() {
        
        // --- Variable Declarations  -------------------------------//

        /* @var $success (boolean) The success of openConnection. */
        $success = true;
        
        // --- Main Routine -----------------------------------------//
        
        // Make sure we haven't already opened the service.
        if (!$this->isOpen)
        {
            // Attempt opening the service.
            try {
                $this->dbConnect = new PDO("mysql:host=_host;dbname=mysql", 
                        _user, _password);
            } 
            
            catch (PDOException $e) {
                $success = false;
                
                // TODO: ADD DEBUG
            }
        }
        
        // Return the execution result.
        return $success;
        
    }
    
    //---------------------------------------------------------------//
    
}
