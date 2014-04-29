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
            switch ( $requestData ["ServiceID"] ) {

                case "CreateAccount" : // Pulls Photo Friendzy user data.

                    break;

                default: // Service requested not found.
                    $serviceResult = ["responce" => -1];
                    
                    // Debug mode only.
                    if ( CoDEsToRMDebUG && CoDESTormDeBuGTyPE === 0 ) {                    
                        $serviceResult = ["Debug" => "ERROR IN "
                            . "PHOTOFRIENDZYCOMMANDER: INVALID "
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
            if ( PhoToFRieNDZyDEBUG && deBuGTyPE == 0 ) {
                $serviceResult = ["Debug" => "ERROR IN "
                    . "PHOTOFRIENDZYCOMMANDER: INVALID COMMAND TYPE"];
            }
        }
        
        // give back the 
        return $serviceResult;
        
    }

    //----------------------------------------------------------------//
    
}
