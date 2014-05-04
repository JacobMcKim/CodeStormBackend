<?php

/*--------------------------------------------------------------------*
* RequestManager.php                                                  *
*---------------------------------------------------------------------*
* Description - This file is used to process all incoming RESTful     *
* requests to the Code Storm Database and affiliated product lines.   *
* All request made for database and backend action must pass through  *
* this script. All content will be secured and its final destination  *
* will be passed along to the addressed system counter parts.         *
*---------------------------------------------------------------------*
* Project: Code Storm LLC. V1.0.01                                    *
* Author : McKim A. Jacob                                             *
* Date Of Creation: 4 - 27 - 2014                                     *
*---------------------------------------------------------------------*
* Copyright © 2014 Code Storm LLC. All Rights Reserved.               *
*---------------------------------------------------------------------*
* NOTICE:  All information contained herein is, and remains           *
* the property of Code Storm LLC. and its suppliers, if any. The      *
* intellectual and technical concepts contained herein are            *
* proprietary to Code Storm LLC. and its suppliers and may be covered *
* by U.S. and Foreign Patents, patents in process, and are protected  *
* by trade secret or copyright law. Dissemination of this information *
* or reproduction of this material is strictly forbidden unless prior *
* written permission is obtained from Code Storm LLC.                 *
* Thank you.                                                          *
*---------------------------------------------------------------------*/

//===================================================================//
//  NOTES & BUGS AS OF 4-27-2014                                     //
//===================================================================//

/*
 * TODO - PHPUNITTESTING - 4/30/14
 * TODO - FIGURE OUT MEDIA UPLOADING. 5/2/14
 */

//===================================================================//
//  Core Program                                                     //
//===================================================================//

// --- Variable Declarations  ---------------------------------------//

/* @var String $incomingData The raw data from the RESTful request. */
$incomingData;

/* @var Array $jsonRequest The assciative array of the request data. */
$jsonRequest;

/* @var Array $serviceResult The array result of the request. */
$serviceResult;

/* @var String $header The content header to send back to the client. */
$header = 'Content-type: application/json';

// --- Main Routine -------------------------------------------------//

 // 1. Pull in the content buffer. 
 $incomingData = file_get_contents('php://input');
 $jsonRequest = json_decode($incomingData);
  
 // Check and see if there is any images to pull as well.
 // TODO : ADD THIS.

 // 2. Determine if the incoming data is a json restful request.
 if ( $jsonRequest != NULL && validateRequest ($jsonRequest) ) {

    // 3. Determine which service is being requested and call it.
    switch ( $jsonRequest ["Service"] ) {
        
        case "CodeStorm" : // Call a Code Storm RESTful service.
            $serviceResult = CodeStormCommander.
                callService ($jsonRequest);
            break;
    
        case "PhotoFriendzy" : // Call a Photo Friendzy RESTful API.
            $serviceResult = PhotoFriendzyCommander.
                callService ($jsonRequest);
            break;
    
        default : // Set the error to incorrect service selected.
            $serviceResult = ["response"=> -1, 
                "debug" => "ERROR: Invalid API Selected."]; 
            break;   
    }
    
    // 4. Strip debug if not needed.
    if (!_cOdeStORMDeBUg && $serviceResult ["debug"] != NULL) {
        unset($serviceResult["debug"]);
    } 

    // 5. Package the response and ship it on its way.
    if ($serviceResult ["header"] != NULL) {
        
        // Set the header.
        $header = $serviceResult["header"];
        unset($serviceResult["header"]);
        header($header);

        // Ship back special data.
        switch ($header) {
            
            case "Picture" :// TODO : Change to actual header info.
                //TODO: Add photo return here.
            break;
        
            default : // Invalid return type.
                requestError (-1);
                break;
        }
    }
    
    else { // Traditional JSON response.
        header($header);
        echo json_encode($serviceResult);
    }
    
}

// Cont 2. Invalid content format.
else {    
    requestError(_cOdeStORMDeBUg ? -1 : 
        "ERROR: Invalid content format.");
}
 
//===================================================================//
//  Utility Functions                                                //
//===================================================================//
 
/******************************************************************
 * @Description - This method is used to determine if the incoming
 * data types fit the parameters they are described as. If one 
 * doesn't fit the bill then return a failed test.
 * 
 * @param $incomingData (Array) - The incoming json data packet in
 * which each element will be scrubbed.
 * 
 * @return The boolean result whether the array is accurate or not.
 * 
 *****************************************************************/ 
function validateRequest ($incomingData) {
    
    // --- Variable Declarations  -----------------------------------//

    /* @var $result (Boolean) The outcome of the scan. */
    $result = true;
    
    // --- Main Routine ---------------------------------------------//
    
    // Insure there is a Service parameter and serviceID parameter.
    if ( $incomingData["Service"] != NULL && 
            $incomingData["ServiceID"] != NULL ) {
        
        $keys = array_keys($incomingData);
        foreach($keys as $key) {
            
            // If it is an email parameter validate that it is an email.
            if (strpos($key,"email")) {
                $result = filter_var($incomingData[$key], 
                        FILTER_VALIDATE_EMAIL);
            }
            
            // Checks that the password is at least 8 characters.
            else if (strpos($key,"password")) {
                $result = count ($incomingData[$key]) >= 8;
            }
            
            // Checks that user id is legit. 
            else if (strpos($key, "userid")) { 
                $result = count ($incomingData[$key]) == 10 && 
                    filter_var($incomingData[$key], FILTER_VALIDATE_INT);
            }
            
            // Exit the search if we find a bad parameter.
            if (!$result) {
                break;
            }
        }
    }
    
    else { // Invalid parameters.
        $result = false;
    }
    
    // Return the result of the function.
    return $result;
    
}

/******************************************************************
 * @Description - Command is used to handle an error being fired 
 * off inside of request manager.
 * 
 * @param $errorCode - The error code fired off by the request 
 * manager that should be returned to the client.
 * 
 * @return None
 * 
 *****************************************************************/ 
function requestError ($errorCode) {
    header('Content-type: application/json');
    echo json_encode( [ "response" => $errorCode ] );
    
}

//===================================================================//
