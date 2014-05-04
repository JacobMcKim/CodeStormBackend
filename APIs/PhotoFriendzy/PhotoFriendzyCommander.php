<?php

/* --------------------------------------------------------------------*
 * PhotoFriendzyCommander.php                                          *
 * --------------------------------------------------------------------*
 * Description - This class is used to provide access to the photo     *
 * friendzy backend API's. It determines which command task to run and *
 * recieves a response back from the command upon completion of its    *
 * given task. It then returns the results back to the Request Manager *
 * for finalized shipping back to the client.                          *
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

class PhotoFriendzyCommander Implements ICommander {
    
    //----------------------------------------------------------------//
    //  Class Function Defintions                                     //
    //----------------------------------------------------------------//
    
    /* This method is used to call out a command and give a response. */
    public static function callService ($requestData) {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $serviceResult Array Contains the command results. */
        $serviceResult = NULL;
        
        // --- Main Routine -----------------------------------------//
        
        // Make sure the serviceID element exists if so execute.
        if ($requestData ["serviceID"] != NULL) {
            
            // Catch any exceptions that arise from the commands.
            try {
                
                // Parse for the right command to be displayed.
                switch ( $requestData ["ServiceID"] ) {

                    case "GetUserData" : // Pulls Photo Friendzy user data.

                        break;

                    case "UpdateUserData" : // Updates Friendzy user data.

                        break;

                    case "GetGames" : // Get players current games list.

                        break;

                    case "CreateGame" : // Creates a new game for player.

                        break;

                    case "DeleteGame" : // Removes a game from game list.

                        break;

                    case "UpdateGame" : // Update a given game.

                        break;

                    case "GetPicture" : // Gets a requested game picture.

                        break;

                    case "CheckNotifications" : // Gets any service notifys.

                        break;

                    default: // Service requested not found.
                        $serviceResult = ["response" => -1];
                        break;
                }
            }
            
            catch (PDOException $pdoE) {
               $serviceResult = ["response" => -1,"debug =>" 
                    ."ERROR IN PHOTOFRIENDZYCOMMANDER: error in "
                    . "db Service :\n"+$pdoE->getMessage()];
            }
            
        }
        
        // Improperly formated request.
        else 
        {
            $serviceResult = ["response" => -1,"debug =>" 
                ."ERROR IN PHOTOFRIENDZYCOMMANDER: Improper "
                . "request format."];
        }
    
        return $serviceResult;
        
    }

    //----------------------------------------------------------------//
    
}
