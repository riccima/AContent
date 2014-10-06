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

//profile_user array 
$profile_user = array();
$profile_user['organization'] = 'ALMA MATER STUDIORUM Univerisity of Bologna' ;
$profile_user['phone'] = '012345678' ;
$profile_user['is_author'] = 1 ;
$profile_user['address'] = 'Via Zamboni 33' ;
$profile_user['city'] = 'Bologna' ;
$profile_user['province'] ='Bo' ;
$profile_user['country'] = 'Italy' ;
$profile_user['postal_code'] = '40126' ;



?>
