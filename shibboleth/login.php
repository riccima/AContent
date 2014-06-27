<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_SHIB_INCLUDE_PATH', 'include/');
define('TR_INCLUDE_PATH', '../include/');
require (TR_INCLUDE_PATH.'vitals.inc.php');

require_once(TR_SHIB_INCLUDE_PATH. 'classes/DAO/ShibUsersDAO.class.php');

$usersDAO = new ShibUsersDAO();

//$user_id=3;
$user_id = $usersDAO->Validate(null, null);
$created=false;

if (!$user_id)
{
	$msg->addError('INVALID_LOGIN');
	header('Location: ../index.php');
	exit;
}
else if ($user_id == -1)
{
	$user_id = $usersDAO->CreateShibUser();
}

$usersDAO->RefreshShibUser($user_id);

if ($usersDAO->getStatus($user_id) == TR_STATUS_DISABLED)
{
	$msg->addError('ACCOUNT_DISABLED');
}
else
{
	$usersDAO->setLastLogin($user_id);
	$_SESSION['user_id'] = $user_id;
	$msg->addFeedback('LOGIN_SUCCESS');
}

if($usersDAO->isUserFieldsMissing($user_id))
	header('Location: profile/index.php');
else
	header('Location: ../index.php');
?>
