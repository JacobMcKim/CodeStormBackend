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
 * TODO - MANAGE DEBuG clause in response objects. 4/30/14
 * TODO - build out the scrub request. 4/30/14
 * TODO - PHPUNITTESTING - 4/30/14
 */

//===================================================================//
//  Includes                                                         //
//===================================================================//

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

// --- Main Routine -------------------------------------------------//

// Set response header data.
 header('Content-type: application/json');

 // 1. Pull in the content buffer. 
 $incomingData = file_get_contents('php://input');
 $jsonRequest = json_decode($incomingData);

 // 2. Determine if the incoming data is a json restful request.
 if ( $jsonRequest != NULL ) {
     
    // 3. Scrub all elments of the json string with anti injection.
    $jsonRequest = scrubRequest($jsonRequest);

    // 4. Determine which service is being requested and call it.
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
            requestError(mAnaGerDebuG ? -1 : 
                "ERROR: Invalid API Selected.");
            break;   
    }

    // 5. Package the response and ship it on its way.
    header($serviceResult["header"]);
    unset($serviceResult["header"]);
    echo json_encode($serviceResult);
}

// Cont 2. Invalid content format.
else {    
    requestError(mAnaGerDebuG ? -1 : 
        "ERROR: Invalid content format.");
}
 
//===================================================================//
//  Utility Functions                                                //
//===================================================================//
 
/******************************************************************
 * @Description - This method is used to scrub all incoming data 
 * packets of any SQL injection characters and strings. Its a 
 * redundant security feature.
 * 
 * @param $incomingData (Array) - The incoming json data packet in
 * which each element will be scrubbed.
 * 
 * @return The scrubbed json array.
 * 
 *****************************************************************/ 
function scrubRequest ($incomingData) {
        
    
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
