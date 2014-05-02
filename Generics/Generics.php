<?php

/* --------------------------------------------------------------------*
 * Generics.php                                                        *
 * --------------------------------------------------------------------*
 * Description - This php file contains generic tools/ resources that  *
 * are for standard use across all components of this web service.     *
 * --------------------------------------------------------------------*
 * Project: Code Storm Backend 1.0.01                                  *
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
 * TODO- CLEAN UP DEBUG 5/2/14
 */

//===================================================================//
//  Global Variables/Constants                                       //
//===================================================================//

/* @var The debug var that activates the Request Manager debug. */
const mAnaGerDebuG = false;

//--------------------------------------------------------------//
// Code Storm Components                                        //
//--------------------------------------------------------------//

/* @var The debug controller that activates code storm debug. */
const CoDEsToRMDebUG = false;

/*--------------------------------------
 * @var CoDESTormDeBuGTyPE int - Controls 
 * the type of debug type being used.
 * 
 * Types: 
 * 0 - Json Error Reporting
 * 1 - MySQL debugging
 * 
 *------------------------------------*/
const CoDESTormDeBuGTyPE = 0;

//--------------------------------------------------------------//
// API Components                                               //
//--------------------------------------------------------------//

/* @var The debug controller that activates Photo Friendzy debug. */
const PhoToFRieNDZyDEBUG = false;

/*--------------------------------------
 * @var deBuGTyPE int - Controls the 
 * type of debug type being used.
 * 
 * Types: 
 * 0 - Json Error Reporting
 * 1 - MySQL debugging
 * 
 *------------------------------------*/
const PhotoFrieNDzYdeBuGTyPE = 0;

//===================================================================//
// Helper Methods                                                    //
//===================================================================//

/******************************************************************
  * @Description - This method is used to randomly generate string
  *  values to be used in security parameters and other various 
  * implmentations in the codestorm API service base.
  * 
  * @param Length - The length of the string to be generated, By
  * default it's 10 characters (Integer). 
  * 
  * @return The randomly generated string (String).
  * 
  *****************************************************************/
function keygen($length=10)
{   
    
    // --- Variable Declarations  -------------------------------//
    
    /* @var $key (String) - The output key generated. */
    $key = '';
    
    /* @var $inputs (String) List of all possible characters to use. */
    $inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));
    
    // --- Main Routine ------------------------------------------//
    
    // Randomly seed the random generator.
    list($usec, $sec) = explode(' ', microtime());
    mt_srand((float) $sec + ((float) $usec * 100000));
 
    // Pick elements from the list and append to the key.
    for($i=0; $i<$length; $i++) {
        $key .= $inputs{mt_rand(0,61)};
    }
    
    // Return the result.
    return $key;
}

//===================================================================//
