<?php

/* --------------------------------------------------------------------*
 * Command.php                                                         *
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
//  NOTES & BUGS AS OF 4-27-2014                                     //
//===================================================================//

/*
 *
 */

//===================================================================//
//  Includes                                                         //
//===================================================================//
include '../Generics/Generics.php';

//===================================================================//
//  Inteface Definition                                              //
//===================================================================//

interface Command {
    
    //----------------------------------------------------------------//
    //  Interface Function Declerations                               //
    //----------------------------------------------------------------//
    
    public function executeCommand ();
    
    function isValidContent ( $Content );
    
    //----------------------------------------------------------------//
    
}
