<?php
/****************************************************************************
 *   Author: Colin Bookman (cobookman@gmail.com)                            *
 *   Copyright 2014                                                         *
 *   Liscense: GPLv3                                                        *
 *                                                                          *
 *   This program is free software: you can redistribute it and/or modify   *
 *   it under the terms of the GNU General Public License as published by   *
 *   the Free Software Foundation, either version 3 of the License, or      *
 *   (at your option) any later version.                                    *
 *                                                                          *
 *   This program is distributed in the hope that it will be useful,        *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of         *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          *
 *   GNU General Public License for more details.                           *
 *                                                                          *
 *   You should have received a copy of the GNU General Public License      *
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.  *
 *--------------------------------------------------------------------------*
 *   Use:                                                                   *
 *   <?php                                                                  *
 *      require_once(...path to CAS.php...);                                *
 *      $cas = new CAS(array(                                               *
 *        'serviceURL' => '...url to where this library is called...',      *
 *        'casURL' => 'https://login.gatech.edu/cas'                        *
 *      ));                                                                 *
 *      $username = $cas->auth();                                           *
 *   ?>                                                                     *
 ****************************************************************************/
namespace cobookman;

Class PHPCAS {
  private $serviceURL;
  private $casURL;
  public function __construct($params) {
    if(!isset($params['serviceURL'])) {
      throw new Exception("For CAS to work you must specify the serviceURL");
    }
    if(!isset($params['casURL'])) {
      throw new Exception("For CAS to work you must specify the casURL, for example: https://login.gatech.edu/cas");
    }
    $this->serviceURL = $params['serviceURL'];
    $this->casURL = rtrim($params['casURL'], '/'); //remove any trailing /
  }
  
  /*
	Returns the username of the logged in user as a string
	or null
  */
  public function getUsername() {
    //must have a ticket, else send user to login form
    if(!isset($_GET['ticket'])) { $this->sendToLogin(); return false; }
    
    $ticket = $_GET['ticket'];
    $url = $this->casURL. '/serviceValidate?ticket='.$ticket.'&service='.$this->serviceURL;

    $res = file_get_contents($url);
    
    //Parse out the XML for the username tags, if they don't exist strpos returns a false
    $startPos = strpos($res,'<cas:user>');
    $endPos = strpos($res, '</cas:user>');

    //The ticket was invalid, and no username sent
    if($startPos === false || $endPos === false) { $this->sendToLogin(); return false;  }  

    //Ticket was valid, parse out the username
    $usernameLen = $endPos - ($startPos+10);
    $username = trim(substr($res, $startPos+10, $usernameLen )); //10 = length of <cas:user>
 
    //username is '' or something that's empty...send back to login as bad input
    if(!strlen($username) >0) { $this->sendToLogin(); return false; } 

    return $username;
  }
  /*
	   Send user to login Prompt, then kill all php processes
  */
  public function sendToLogin() { 
    header('Location: ' .$this->casURL . '/login?service='.$this->serviceURL); die();
  }
  /*
    if you successfully logged in, then sets basic http auth server params, 
    else sends user ot the login form
      $_SERVER['REMOTE_USER'] = $username
      $_SERVER['PHP_AUTH_PW'] = sha512($username)
  */
  public function auth() {
    $username = $this->getUsername();
    if($username) {
      $_SERVER["REMOTE_USER"] = $username;
      $_SERVER["PHP_AUTH_PW"] = hash('sha512', $username);
    } else {
      $this->sendToLogin();
    }
    return $username;
  }
}

?>
