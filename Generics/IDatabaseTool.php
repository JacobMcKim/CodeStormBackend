<?php

/* --------------------------------------------------------------------*
 * IDatabaseTool.php                                                   *
 * --------------------------------------------------------------------*
 * Description - This interface is used to lay the foundation for all  *
 * other database accessing objects utilized in Code Storm product     *
 * APIs. These classes will insure that database access is             *
 * encapsulated in an isolated enviorment while still providing a      *
 * robust and flexible enviorment for accessing content.               *
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
include '../Generics/Generics.php';

interface IDatabaseTool {

    //----------------------------------------------------------------//
    //  Interface Function Declerations                               //
    //----------------------------------------------------------------//

    /******************************************************************
     * @Description - This method is to be internally used by the 
     * class implementing IDatabaseTool to establish a connection with 
     * the web db service it's attempting to connect to.
     * 
     * @param None 
     * 
     * @return Whether or not it was successful at establishing a
     * connection with the web service (boolean).
     * 
     *****************************************************************/ 
    function openConnection ();

    /******************************************************************
    * @Description - This method is to be internally used by the 
    * class implementing IDatabaseTool to close an established
    *  connection with the web db service it's connected to.
    * 
    * @param None 
    * 
    * @return Whether or not it was successful at closing the 
    * connection (boolean).
    * 
    *****************************************************************/ 
    function closeConnection ();
    
    /******************************************************************
    * @Description - This method is used to fire off command queries to
    * the database services its accessing.
    * 
    * @param $RequestString (String)- The command string to be 
    * executed inside the DB service.
    * 
    * @param $RequestAtributes (associativeArray) - The atributes to
    * be included into the query if any.
    * 
    * @return The success or failure of the query executed (Boolean).
    * 
    *****************************************************************/
    function executeQuery ($RequestString, $RequestAtributes);
    
    /******************************************************************
    * @Description - This method is used to fetch a set of results 
    * from a command issued to the data base service.
    * 
    * @param $RequestString (String)- The command string to be 
    * executed inside the DB service.
    * 
    * @param $RequestAtributes (associativeArray) - The atributes to
    * be included into the query if any.
    * 
    * @return The success or failure of the query executed (Boolean).
    * 
    *****************************************************************/ 
    function executeFetch ($RequestString, $RequestAtributes);
    
    /******************************************************************
     * @Description - This method depending on the type of query will 
     * return the results that was given back from the database.
     * 
     * @param None 
     * 
     * @return An asscoiative array of results fed back from the data
     * base.
     * 
     *****************************************************************/ 
    //function getResponce ();
    
    //----------------------------------------------------------------//
    
}
