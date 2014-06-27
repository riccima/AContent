<?php

define('TR_INCLUDE_PATH', '../../include/');
define('TR_SHIB_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_SHIB_INCLUDE_PATH.'classes/DAO/ShibUsersDAO.class.php');

global $_current_user;

$usersDAO = new ShibUsersDAO();

if (!isset($_current_user))
{
	require(TR_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('INVALID_USER');
	require(TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	Header('Location: ../../index.php');
	exit;
}

if (isset($_POST['submit'])) {
	if (isset($_POST['is_author'])) $is_author = 1;
	else $is_author = 0;
		
	$usersDAO = new ShibUsersDAO();
	$user_row = $usersDAO->getUserByID($_SESSION['user_id']);
	
	if ($usersDAO->Update($_SESSION['user_id'], 
	                  $user_row['user_group_id'],
                      $user_row['login']==null?$_POST['login']:$user_row['login'],
	                  $user_row['email']==null?$_POST['email']:$user_row['email'],
	                  $_POST['first_name'],
	                  $_POST['last_name'],
                      $is_author,
                      $_POST['organization'],
                      $_POST['phone'],
                      $_POST['address'],
                      $_POST['city'],
                      $_POST['province'],
                      $_POST['country'],
                      $_POST['postal_code'],
	                  $user_row['status']))
	
	{
		$msg->addFeedback('PROFILE_UPDATED');
	}
	$usersDAO->RefreshShibUser($_SESSION['user_id']);
}

$row = $_current_user->getInfo();

if (!isset($_POST['submit'])) {
	$_POST = $row;
}

/* template starts here */
$savant->assign('row', $row);

global $onload;
$onload = 'document.form.first_name.focus();';

$savant->display('profile/shibboleth_profile.tmpl.php');
?>
