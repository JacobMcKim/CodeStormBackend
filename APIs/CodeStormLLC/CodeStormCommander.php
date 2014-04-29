<?php

/* --------------------------------------------------------------------*
 * CodeStormCommander.php                                              *
 * --------------------------------------------------------------------*
 * Description - This class is used to provide access to the Code      *
 * Storm LLC. backend API's. It determines which command task to run   *
 * and recieves a responce back from the command upon completion of    *
 * its given task. It then returns the results back to the Request     *
 * manage for finalized shipping back to the client.                   *
 * --------------------------------------------------------------------*
 * Project: Photo Friendzy™ 2.0.01                                     *
 * Author : McKim A. Jacob                                             *
 * Date Of Creation: 4 - 27 - 2014                                     *
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

class CodeStormCommander Implements Commander {
    
    //----------------------------------------------------------------//
    //  Class Function Defintions                                     //
    //----------------------------------------------------------------//
    
    /* This method is used to call out a command and give a responce. */
    public static function callService ($requestData) {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $serviceResult Array Contains the command results. */
        $serviceResult = array ();
        
        // --- Main Routine -----------------------------------------//
        
        // Make sure the serviceID element exists if so execute.
        if ($requestData ["serviceID"] != NULL) {
            
            // Set the default header type. Can be changed for pictures.
            $serviceResult = ["header" => "Content-type: application"
                . "/json"];
            
            // Parse for the right command to be displayed.
            switch ( $requestData ["ServiceID"] ) {
                
                case "CreateAccount" : // Creates a Code Storm account.

                    break;
                
                case "Login" : // Signs user in and creates a session.
                    
                    break;
                
                case "Logout" : // Signs user out and destroys session.
                    
                    break;
                
                case "UpdateUserData" : // Updates a users credentials.
                    
                    break;
                
                case "GetUserData" : // Gets Data about the user.
                    
                    break;
                
                case "ForgotPassword" : // Configures a password change.
                    
                    break;
                
                case "ChangePassword" : // Changes a users password.
                    
                    break;
                
                case "ChangeProfilePicture" : // Changes the profile picture.
                    
                    break;
                
                case "GetProfilePicture" : // Gets the profile picture. 
                    
                    break;

                default: // Service requested not found.
                    $serviceResult = ["responce" => -1];
                    
                    // Debug mode only.
                    if ( CoDEsToRMDebUG && CoDESTormDeBuGTyPE == 0 ) {                    
                        $serviceResult = ["Debug" => "ERROR IN "
                            . "CODESTORMCOMMANDER: INVALID "
                            . "COMMAND TYPE"];
                    }
                    break;
            }
        }
        
        // Improperly formated request.
        else
        {
            $serviceResult = ["responce" => -1];
            
            // Debug mode only.
            if ( CoDEsToRMDebUG && CoDESTormDeBuGTyPE == 0 ) {
                $serviceResult = ["Debug" => "ERROR IN "
                    . "CODESTORMCOMMANDER: INVALID COMMAND TYPE"];
            }
        }
        
        // give back the 
        return $serviceResult;
        
    }

    //----------------------------------------------------------------//
    
}
