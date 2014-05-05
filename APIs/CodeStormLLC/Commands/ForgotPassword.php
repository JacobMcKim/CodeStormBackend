<?php

/* --------------------------------------------------------------------*
 * ForgotPassword.php                                                  *
 * --------------------------------------------------------------------*
 * Description - This class is used to generate change password        *
 * requests for codestorm users. It verifies and sends emails to the   *
 * client as well to notify them of the change request.                *
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
 * TODO - Modify message body of html email message. 5/2/14
 */

/*
 *+---------------------------------------------+
 *|             Command Outputs                 |
 *+-----------+---------------------------------+
 *| Response  | Description                     |
 *+-----------+---------------------------------+
 *|    -1     | Failed to complete somewhere.   |
 *|     1     | Succeeded at execution.         |
 *|     2     | Did not find account.           |
 *+-----------+---------------------------------+
 */

class ForgotPassword Extends Command {

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
        $commandParams = array ("email");
        
        /* @var $commandResult (Array) The result of the command.     */
        $commandResult = NULL;
        
        /* @var $result (object) The output of PDO sql executes.      */
        $result = NULL;
        
        /* @var $sessionResult (object) The result of session create. */
        $sessionResult = NULL;
                
        /* @var $sqlQuery (object) The query to execute on service.   */
        $sqlQuery = NULL;

        // --- Main Routine ------------------------------------------//
        
        // Check if the request contains all necessary parameters.
        if ( $this->isValidContent ($this->requestContent, $commandParams) ) {
        
            try {
                // Query for the email.
                $sqlQuery = "SELECT UserID, Username FROM CodestormUsers. "
                        . "Accounts WHERE Email = :email AND AccountType ="
                        . " 'DEFAULT' LIMIT 1";
                $result = $this->dbAccess->executeFetch($sqlQuery, 
                        ["email" => $this->requestContent["email"]]);

                // If we found a match create a new iForgot in the table.
                if ( $result ["UserID"] != NULL ) {

                    // Generate security key.
                    $securityCode = keygen(24);

                    $sqlQuery = "INSERT INTO CodestormUsers.iForgots "
                        . "(UserID, SecurityCode, ExpireTime) VALUES "
                        . "(:userid, :securitycode, TIMESTAMPADD "
                        . "(HOUR, 2, CURRENT_TIMESTAMP))";
                    $sessionResult = $this->dbAccess->executeQuery($sqlQuery,
                        ["userid" => $result["UserID"], "securitycode" => 
                            $securityCode]);

                    // Determine result of the query.
                    if ($sessionResult == true && 
                            $this->mailRequest($result, $securityCode) ) {
                        $commandResult = ["response" => 1];
                    }
                    else {
                        $commandResult = ["response" => -1, "debug" => 
                        "IN FORGOTPASSWORD - Query/Mail came back false." ];
                    }
                }
                
                else { // Account not found.
                    $commandResult = ["response" => 2];
                }
            }
            
            catch (PDOException $pdoE) { // Error occured in query.
                $commandResult = ["response" => -1, "debug" => 
                    "IN ForgotPassword -" + $pdoE->getMessage()];
            }
        }
        
        else { // Invalid content.
            $commandResult = ["response" => -1, "debug" => 
                "IN FORGOTPASSWORD - command invalid." ];
        }
        
        // return the result of the command.
        return $commandResult;
        
    }
    
    
    /******************************************************************
    * @Description - Called to email the user requesting a password
    * rest the link to reset their accounts password.
    * 
    * @param $requestData - The json request data required to make the 
    * email.
    * 
    * @param $SecurityCode - The code that will be used to preform
    * the change password request.
    * 
    * @return None
    * 
    *****************************************************************/
    private function mailRequest ( $requestData, $SecurityCode ) {

        // --- Variable Declarations  -------------------------------//
                
        /* @var $headers (String) - Meta data to be sent with email. */
        $headers = '';
                
        /* @var $message (String) - The HTML Body of the message.    */
        $message = '';

        /* @var $subject (String) - What the message is about.       */
        $subject = 'Did you forget your password?';
        
        /* @var $to (String) - Who to send the email to.             */
        $to  = $this->requestContent["email"];
        
        // --- Main Routine ------------------------------------------//
        
        // Construct the HTML message body.
        $message .= '
        <html>
            <head>
              <title>Forgot Password?</title>
            </head>
            <body>
                <h3>Hi '.$requestData["Username"].',</h3>
                <p> A request to change for to reset your password was 
                submitted. To reset your password please click the following
                link... </p>
                <h2><a href="https://resetpassword.codestormllc.com"
                >Reset Your Password</a></h2> 
                <h1><p></p></h1>

                    <h4>Your Security Code is: </h4>
                    <h3>'.$SecurityCode.'</h3>
                <h1><p></p><p> </p></h1>
                <h4>Thank you,</h4>
                <h4>The Code Storm Team </h4>
            </body>
        </html>
        ';

        // Build headers.
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
        $headers .= 'To:'.$this->requestContent["email"]." \r\n";
        $headers .= 'From: Code Storm LLC.'
                . ' <pleasedonotreply@CodeStormLLC.com>'."\r\n";

        // Send the email to the user.
        return mail($to, $subject, $message, $headers);   
        
    }

    //---------------------------------------------------------------//
}
