<?php

/* --------------------------------------------------------------------*
 * Commander.php                                                       *
 * --------------------------------------------------------------------*
 * Description - This interface is used as the foundation for all      *
 * service commanders in any backend project of the code storm API web *
 * services. The commander object serves as the accessor tool in which *
 * executes and runs commands for a given requested service. In which  *
 * then also returns a responce of the given request to the request    *
 * manager at the front of the web server for distribution back to the * 
 * client.                                                             *
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

//===================================================================//
//  Inteface Definition                                              //
//===================================================================//

interface Commander {
    
    //----------------------------------------------------------------//
    //  Interface Function Declerations                               //
    //----------------------------------------------------------------//
    
    public static function callService ( $requestData );
    
    //----------------------------------------------------------------//
    
}
