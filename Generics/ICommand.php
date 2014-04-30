<?php

/* --------------------------------------------------------------------*
 * ICommand.php                                                        *
 * --------------------------------------------------------------------*
 * Description - This interface is used as the foundation for all      *
 * commands executed in the code storm cloud enviorment. All command   * 
 * services must model after this interface in order to be utilized.   *
 * --------------------------------------------------------------------*
 * Project: Code Storm Backend 1.0.01                                  *
 * Author : McKim A. Jacob                                             *
 * Date Of Creation: 4 - 28 - 2014                                     *
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


interface ICommand {
    
    //---------------------------------------------------------------//
    //  Interface Function Declerations                              //
    //---------------------------------------------------------------//
    
    /******************************************************************
    * @Description - A method called to execute the request API 
    * command. This is where the magic happens in the command object.
    * 
    * @return A json array result of the command's execution.
    * 
    *****************************************************************/ 
    public function executeCommand ();
    
    /******************************************************************
    * @Description - A method called to validate that an incomming API
    * request has all the nessisary parameters to preform the request
    * at hand.
    * 
    * @param $Content - The json content of the request to be cross 
    * referenced with the expected result.
    *
    * @param $arrayParams - The array of keys that should be expected
    * in the json request object.
    * 
    * @return Whether the request parameters are all there for 
    * executing the request. (True - all there. False - Not all there)
    * 
    *****************************************************************/ 
    function isValidContent ( $Content, $arrayParams );
    
    //----------------------------------------------------------------//
    
}
