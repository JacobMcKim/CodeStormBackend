<?php

/* --------------------------------------------------------------------*
 * AccountsDBTool.php                                                  *
 * --------------------------------------------------------------------*
 * Description - This class is used to interface Code Storm's user     *
 * accounts database. This class can be created by API services for    *
 * accessing and modifing data stored in the Accounts database         *
 * service.                                                            *
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
//  Includes                                                         //
//===================================================================//

class AccountsDBTool extends MySqlDatabaseTool {

    //---------------------------------------------------------------//
    // Class Atributes                                               //
    //---------------------------------------------------------------//
        
    /* The host address of the database service. */
    const _host = "127.00.00.01:3306";
    
    /* The account username to use when signing into the service. */
    const _user = "root";
    
    /* The account password to use when signing into the service. */
    const _password = "root";
    
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
    function __construct() {
        parent::__construct();

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
        parent::__destruct();
        
    }
    
    //---------------------------------------------------------------//
    
}
