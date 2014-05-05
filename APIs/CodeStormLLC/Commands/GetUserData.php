<?php

/* --------------------------------------------------------------------*
 * GetUserData.php                                                     *
 * --------------------------------------------------------------------*
 * Description - This class is used to search individual user          *
 * atributes from various data sources and provide a summary feedback  *
 * about the given user. Currently it is a per user per data type      *
 * search basis. It will peice together results from a given array for *
 * a given table.                                                      *
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
 *|     2     | Did not find anything with give.|
 *+-----------+---------------------------------+
 */

class GetUserData extends Command {

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
    * @Description - This method constructs a query list for data 
    * being asked for in the Accounts table.
    * 
    * @param $terms (array) - The list of terms to parse through and  
    * put together in the search string.
    * 
    * @return Returns the partial SQL string with the items selected
    * for searching.
    * 
    *****************************************************************/
    private function accountQuery ($sqlString, $terms) {
        
        // --- Variable Declarations  --------------------------------//
        
        /* @var $sqlString ($string) The list of items to search.     */
        $sqlString = "";
        
        /* @var $term ($string) The present value to add to list.     */
        $term = "";
        
        // --- Main Routine ------------------------------------------//
        
        // Loop through each term and add it to the list.
        foreach ($term as $terms) {
            switch ($term) {
                case "userid":
                    $sqlString.= " UserID";
                    break;
                case "username" :
                    $sqlString.= " Username";
                    break;

                case "facebookid" :
                    $sqlString.= " FacebookID";
                    break;

                case "coins" :
                    $sqlString.= " Coins";
                    break;

                case "type" : 
                    $sqlString.= " AccountType";
                    break;
                default : // Key not found.
                    break;
            }
            
            // Add a comma for each aditional element.
            $sqlString.= ",";
        }
        
        // Remove the last comma in the string.
        rtrim($sqlString, ',');
        
        // Return the result.
        return $sqlString;
        
    }
    
    
    /* Executes the command defined for the service implementation. */
    public function executeCommand() {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $commands (Array) Used to cross check the request.   */
        $commandParams = array ("userid", "sessionid", "requesttype",
            "searchables");
        
        /* @var $commandResult (Array) the result of this command.   */
        $commandResult = NULL;
                
        /* @var $conditionsSql (String) Conditions to be met.        */
        $conditionsSql = "";
        
        /* @var $findResult (Array) The result of the item search.   */
        $findResult = NULL;
        
        /* @var $searchSql (String) The elements to search.          */
        $searchSql = "SELECT";
        
        /* @var $toSql (String) What table to search in.             */
        $toSql = "";

        // --- Main Routine ------------------------------------------//
        
        // Validate the request parameters and that a session exists.
        if ( $this->isValidContent ($this->requestContent, $commandParams)  &&
            $this->checkSession ($this->dbAccess, $this->requestContent
            ["userid"], $this->requestContent["sessionid"]) && 
            count($this->requestContent["searchables"]) > 0 ) {
            
            // Select the table type and call helper function.
            switch ($this->requestContent["requesttype"]) {
                case "account" :
                    $searchSql = $this->accountQuery ($searchSql, $this->
                            requestContent["searchables"]); 
                    $toSql = "FROM CodestormUsers.Accounts WHERE";

                    break;
                case "session" :
                    $searchSql = $this->sessionQuery ($searchSql, $this->
                            requestContent["searchables"]); 
                    $toSql = "FROM CodestormUsers.Sessions WHERE";
                    break;

                case "forgots" :
                    $searchSql = $this->forgotsQuery ($searchSql, $this->
                            requestContent["searchables"]);
                    $toSql = "FROM CodestormUsers.iForgots WHERE";
                    break;

                default : // Invalid table miss match.
                    break;                    
            }
                
            // Decide which search pattern to use.
            if (filter_var($this->requestContent["search"], 
                    FILTER_VALIDATE_EMAIL)) {
                $conditionsSql = "Email = :data";
            }
            else if (filter_var($this->requestContent["search"],
                    FILTER_VALIDATE_INT) ) {
                $conditionsSql = "UserID = :data";
            }

            // make sure we append the search parameters.
            if ($searchSql != "SELECT" && $toSql != "") {
                
                // Build SQL Querry.
                $searchSql.=$toSql.$conditionsSql;

                // Attempt Search for elements.
                try {
                    $findResult = $this->dbAccess->executeFetch 
                        ($searchSql, ["data" => $this->requestContent 
                        ["search"]]);
                    
                    if ($findResult != NULL) {
                        $commandResult = $findResult;

                    }
                    
                    else { // Did not succeed with request.
                        $commandResult = ["response" => 2];
                    }  
                } 

                catch (PDOException $pdoE) { // Error occured in request.
                    $result = ["response" => -1, "debug" => 
                        "IN GETUSERDATA - " + $pdoE->getMessage()]; 
                }

            }
            
            else { // Nothing queried.
                $commandResult = ["response" => -1,  "debug" => 
                "IN GETUSERDATA - Nothing to query." ];
            }
        }
        
        else { // Invalid parameters and or session.
            $commandResult = ["response" => -1, "debug" => 
                "IN GETUSERDATA - command invalid." ];
        }
        
        // Return the result of this commands execution.
        return $commandResult;
        
    }
    
    
    /******************************************************************
    * @Description - This method constructs a query list for data 
    * being asked for in the iForgot table.
    * 
    * @param $terms (array) - The list of terms to parse through and  
    * put together in the search string.
    * 
    * @return Returns the partial SQL string with the items selected
    * for searching.
    * 
    *****************************************************************/
    private function forgotsQuery ($terms) {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $sqlString ($string) The list of items to search.    */
        $sqlString = "";
        
        /* @var $term ($string) The present value to add to list.    */
        $term = "";
        
        // --- Main Routine -----------------------------------------//
        
        // Loop through each term and add it to the list.
        foreach ($term as $terms) {
            switch ($term) {
                case "userid":
                    $sqlString.= " UserID";
                    break;
                case "endtime" :
                    $sqlString.= " ExpireTime";
                    break;
                default : // Not found.
                    break;
            }
            
            // Add a comma for each aditional element.
            $sqlString.= ",";
        }
        
        // Remove the last comma in the string.
        rtrim($sqlString, ',');
        
        // Return the result.
        return $sqlString;
        
    }
    
    
    /******************************************************************
    * @Description - This method constructs a query list for data 
    * being asked for in the sessions table.
    * 
    * @param $terms (array) - The list of terms to parse through and  
    * put together in the search string.
    * 
    * @return Returns the partial SQL string with the items selected
    * for searching.
    * 
    *****************************************************************/
    private function sessionQuery ($sqlString, $terms) {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $sqlString ($string) The list of items to search.    */
        $sqlString = "";
        
        /* @var $term ($string) The present value to add to list.    */
        $term = "";
        
        // --- Main Routine -----------------------------------------//
        
        // Loop through each term and add it to the list.
        foreach ($term as $terms) {
            switch ($term) {
                case "userid":
                    $sqlString.= " UserID";
                    break;
                case "starttime" :
                    $sqlString.= " StartTime";
                    break;

                case "endtime" :
                    $sqlString.= " ExpireTime";
                    break;
                default : // Not found.
                    break;
            }
            
            // Add a comma for each aditional element.
            $sqlString.= ",";
        }
        
        // Remove the last comma in the string.
        rtrim($sqlString, ',');
        
        // Return the result.
        return $sqlString;
        
    }
    
    //---------------------------------------------------------------//
    
}
