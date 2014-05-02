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

class CodeStormCommander Implements ICommander {
    
    //----------------------------------------------------------------//
    //  Class Function Defintions                                     //
    //----------------------------------------------------------------//
    
    /* This method is used to call out a command and give a responce. */
    public static function callService ($requestData) {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $serviceResult Array Contains the command results. */
        $serviceResult = ["header" => "Content-type: application"
                . "/json"];
        
        // --- Main Routine -----------------------------------------//
        
        // Make sure the serviceID element exists if so execute.
        if ($requestData ["serviceID"] != NULL) {
            
            // Catch any exceptions that arise from the commands.
            try {
                        
                // Parse for the right command to be displayed.
                switch ( $requestData ["ServiceID"] ) {

                    case "CreateAccount" : // Creates a Code Storm account.
                        $iCommand = new CreateAccount ($requestData);
                        break;

                    case "Login" : // Signs user in and creates a session.
                        $iCommand = new Login ($requestData);
                        break;

                    case "Logout" : // Signs user out and destroys session.
                        $iCommand = new Logout ($requestData);
                        break;

                    case "UpdateUserData" : // Updates a users credentials.
                        $iCommand = new UpdateUserData ($requestData);
                        break;

                    case "GetUserData" : // Gets Data about the user.
                        $iCommand = new GetUserData ($requestData);
                        break;

                    case "ForgotPassword" : // Configures a password change.
                        $iCommand = new ForgotPassword ($requestData);
                        break;

                    case "ChangePassword" : // Changes a users password.
                        $iCommand = new ChangePassword ($requestData);
                        break;

                    case "ChangeProfilePicture" : // Change the profile picture.
                        $iCommand = new ChangeProfilePicture ($requestData);
                        break;

                    case "GetProfilePicture" : // Gets the profile picture. 
                        $iCommand = new GetProfilePicture ($requestData);
                        break;

                    default: // Service requested not found.
                        $serviceResult = ["responce" => -1,"debug =>" 
                            ."ERROR IN CODESTORMCOMMANDER: INVALID "
                            . "COMMAND TYPE"];
                        break;
                }

                // Execute command.
                $serviceResult = $iCommand -> executeCommand();
            }
            
            catch (PDOException $pdoE) {
               $serviceResult = ["responce" => -1,"debug =>" 
                    ."ERROR IN CODESTORMCOMMANDER: error in "
                    . "db Service :\n"+$pdoE->getMessage()];
            }
            
        }
        
        // Improperly formated request.
        else
        {
            $serviceResult = ["responce" => -1,"debug =>" 
                ."ERROR IN CODESTORMCOMMANDER: Improper "
                . "request format."];
        }
        
        // give back the result.
        return $serviceResult;
        
    }

    //----------------------------------------------------------------//
    
}
