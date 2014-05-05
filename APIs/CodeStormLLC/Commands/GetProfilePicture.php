<?php

/* --------------------------------------------------------------------*
 * GetProfilePicture.php                                               *
 * --------------------------------------------------------------------*
 * Description - This class is used to obtain a requested user         *
 * profile picture from the code storm database of user photos.        *
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
//  NOTES & BUGS AS OF 4-29-2014                                     //
//===================================================================//

/*
 * TODO - ADD DEBUG TO GETIMAGE 5/4/14
 * TODO - FINSIH S3 STUFF AND RESPONSE 5/4/14
 */

/*
 *+---------------------------------------------+
 *|             Command Outputs                 |
 *+-----------+---------------------------------+
 *| Response  | Description                     |
 *+-----------+---------------------------------+
 *|    -1     | Failed to complete somewhere.   |
 *|     1     | Succeeded at execution.         |
 *|     2     | Account not found.              |
 *|     3     | Use facebook profile image.     |
 *+-----------+---------------------------------+
 */


//===================================================================//
//  Includes                                                         //
//===================================================================//
require 'aws-autoloader.php';
use Aws\S3\S3Client;

//===================================================================//


class GetProfilePicture extends Command {
    
    //---------------------------------------------------------------//
    // Class Atributes                                               //
    //---------------------------------------------------------------//
    
    /* @var $dbAccess () The database access object linking to DB.   */
    private $dbAccess;
    
    /* @var $requestContent (Array) The content of the user request. */
    private $requestContent = array();
    
    //---------------------------------------------------------------//
    // Constructor/Destructors                                       //
    //---------------------------------------------------------------//

    /******************************************************************
     * @Description - Called to build the logout request command, It 
     * takes in the command parameters and saves them locally to the 
     * class.
     * 
     * @param $requestData - The json request data required to make the 
     * request.
     * 
     * @return None
     * 
     *****************************************************************/
    function __construct($requestData) {
        
        // Set the content locally.
        $this->requestContent = $requestData;
        
        // Create the new required database objects to preform task.
        $this->dbAccess = new AccountsDBTool ();

    }
    
    /******************************************************************
     * @Description - Called when the command has finished executing
     * and its time to tear down all the command's resources.
     * 
     * @param None 
     * 
     * @return None
     * 
     *****************************************************************/   
    function __destruct() {
        $this->dbAccess = NULL;
        $this->requestContent = NULL;
        
    }

    //---------------------------------------------------------------//
    // Class Methods                                                 //
    //---------------------------------------------------------------//
    
    /* Executes the command defined for the service implementation. */
    public function executeCommand() {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $commands (Array) Used to cross check the request.    */
        $commandParams = array ("userid", "sessionid", "profileuserid");
        
        /* @var $commandResult (Array) The result of the command.     */
        $commandResult = NULL;
        
        /* @var $image (Image) The image pulled for the users profile. */
        $image = NULL;
        
        /* @var $result (object) The output of PDO sql executes.      */
        $result = NULL;
        
        /* @var $sqlQuery (object) The query to execute on service.   */
        $searchSql = NULL;
        
        // --- Main Routine ------------------------------------------//
        
        // Check if the request contains all necessary parameters.
        if ( $this->isValidContent ($this->requestContent, $commandParams) && 
            $this->checkSession($this->dbAccess, $this->requestContent
            ["userid"], $this->requestContent["sessionid"]) ) {
        
            // Find the profile image location.
            try {
                $searchSql = "SELECT ProfileImage, FacebookID FROM "
                    . " CodestormUsers.Accounts WHERE Userid = "
                    . ":profileid LIMIT 1";
                $result = $this->dbAccess->executeFetch ($searchSql, 
                        $this->requestContent ["profileuserid"]);

                // See if it is a facebook profile image.
                if ($result["ProfileImage"] == "facebook") {
                    $commandResult = ["response" => 3, 
                        "facebookid" => $result["FacebookID"] ];
                }
                
                // Pull the image from AWS cloud.
                else if ($result["ProfileImage"] != NULL) { 
                    
                    $image = $this->getProfileImage($result["ProfileImage"]);
                    
                    // Check that the image pull worked.
                    if ($image != NULL) {
                        $commandResult = ["response" => 1,
                            "header" => "picture", "picture" => $image ];
                    }
                    else {
                        $commandResult = ["response" => -1, "debug" =>
                            "IN GETPROFILEPICTURE - Image could not"
                            . " be accessed."];
                    }
                }
                
                // Could not find the account.
                else {
                    $commandResult = ["response" => 2];
                }
            } 
            
            catch (PDOException $pdoE) { // Request Failed.
                $commandResult = ["response" => -1, "debug" => 
                    "IN GETPROFILEPICTURE - " + $pdoE->getMessage()];
            }
        }
    }
    
    
    /******************************************************************
    * @Description - This method reaches out to S3 and pulls the 
    * profile image of the user.
    * 
    * @param $imageID (String) - The image identification to be used 
    * to find the image in the bucket on S3.
    * 
    * @return The profile image of the user or null if not found.
    * 
    *****************************************************************/ 
    private function getProfileImage ( $imageID ) {
        
        // --- Variable Declarations  -------------------------------//
        
        /* @var $client (S3Client) The connection to AWS S3 Server.  */
        $client = NULL;
                
        /* @var $image (Object) The actual image to return.          */
        $image = NULL;
        
        /* @var $profilebucket (Array) The result of the s3 query.   */
        $imageResult = NULL;
        
        /* @var $photoPrefix (String) The photo location prefix.     */
        $photoPrefix = "ProfilePicture/";
        
        /* @var $profilebucket (String) The S3 Bucket name.          */
        $profilebucket = "CodeStormLLC";

        // --- Main Routine ------------------------------------------//
        
        try {
                
            // Connect to AWS.
            $client = S3Client::factory(array(
            'base_url' => _profileHost,
            'key'      => _profileAWSKey,
            'secret'   => _profileSecretKey
            ));

            // Grab image. 
            if ($client != NULL) {

                $image = $client->getObject ( array(
                    'Bucket' => $profilebucket,
                    'Key' => $photoPrefix.$imageID.".png") );

                // Give back the result.
                $imageResult = $image ["Body"];

            }
            
            // Disconnect.
            $client = NULL;
        }
        
        catch (S3Exception $s3E) {
           // TODO - ADD A DEBUG HERE.
        }
        
        // Return result.
        return $imageResult;
        
    }
    
    //---------------------------------------------------------------//

}
