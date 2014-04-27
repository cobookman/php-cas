<?php
require 'vendor/autoload.php';

$cas = new cobookman\PHPCAS(array(
  'serviceURL' => 'http://critique.gatech.edu', 
  'casURL' => 'https://login.gatech.edu/cas'    
));
$cas->auth();

?>
