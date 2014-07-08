<?php 

if (!defined('TR_INCLUDE_PATH')) { exit; }

/* The user identifier attribute*/
$_shib_user_identifier='eppn';
$_acontent_user_identifier='email';

/* Shib attribute mapped into AContent user property 
   will be refreshed on every login
*/
$_shib_acontent_attribute_map = array();
//example using TestShib IdP
$_shib_acontent_attribute_map['HTTP_GIVENNAME']='first_name';
$_shib_acontent_attribute_map['HTTP_SN']='last_name';

?>
