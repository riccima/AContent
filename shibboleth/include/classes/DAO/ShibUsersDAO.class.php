<?php

/**
 * DAO for "shib users"
 * @access	public
 * @author	Valentino Ricci
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH') || !defined('TR_SHIB_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/UsersDAO.class.php');
require_once(TR_INCLUDE_PATH. 'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH. '../shibboleth/config.inc.php');

class ShibUsersDAO extends UsersDAO {

	/**
	 * Validate if the given login/pwd is valid
	 * @access  public
	 * @param   login: login id or email
	 *          pwd: password
	 * @return  user id, if login/pwd is valid
         *          -1 if user must be created
	 *          false, if login/pwd is invalid
	 * @author  Valentino Ricci
	 */
	public function Validate($login, $pwd)
	{
		global $_shib_user_identifier;

		// If we are in the shibboleth directory then we trust the server var
		if (!empty($_SERVER[$_shib_user_identifier])) {
			// Associate Shibboleth session with user for SLO preparation
			$sessionkey = false;
			if (isset($_SERVER['Shib-Session-ID'])){
				// This is only available for Shibboleth 2.x SPs
				$sessionkey = $_SERVER['Shib-Session-ID'];
			} else {
				// Try to find out using the user's cookie
				foreach ($_COOKIE as $name => $value){
					if (preg_match('/_shibsession_/i', $name)){
						$sessionkey = $value;
					}
				}
			}
			if(!$sessionkey)
				return false;

			// Set shibboleth session ID for logout
			$_SESSION['shibboleth_session_id']  = $sessionkey;
			$user = $this->getUserByShibID();
			
			if(!$user)
			{
				return -1;
			}
			return $user['user_id'];
		}
		return false;
	}

	/**
	 * Return user information by given user id
	 * @access  private
	 * @return  user row
	 * @author  Valentino Ricci
	 */
	private function getUserByShibID()
	{
		global $_shib_user_identifier;
		global $_acontent_user_identifier;

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'users WHERE '.$_acontent_user_identifier.'=\''.$_SERVER[$_shib_user_identifier].'\'';

		if ($rows = $this->execute($sql))
		{
			return $rows[0];
		}
		else return false;
	}

	/**
	 * Create new shib user
	 * @access  public
	 * @return  user id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Valentino Ricci
	 */
	public function CreateShibUser()
	{
		global $_shib_user_identifier;
		global $_acontent_user_identifier;
		
		$additional_col_names = "";
		$additional_col_values = "";
		$mapped_attributes = $this->getMappedShibAttributes();
		if(count($mapped_attributes)>0)
		{
			$additional_col_names = implode(",",array_keys($mapped_attributes)).",";
			$additional_col_values = implode(",",array_values($mapped_attributes)).",";
		}
				
		/* insert into the db */
		$sql = "INSERT INTO ".TABLE_PREFIX."users
		              (".$additional_col_names
		               .$_acontent_user_identifier.",
					   user_group_id,
		               web_service_id,
		               status,
		               create_date
		               )
		       VALUES (".$additional_col_values
					   ."'".$_SERVER[$_shib_user_identifier]."',
					   2,
		               '".Utility::getRandomStr(32)."',
		               1, 
		               now())";

		if (!$this->execute($sql))
		{
			$msg->addError('DB_NOT_UPDATED');
			return false;
		}
		else
		{
			return mysql_insert_id();
		}
	}
	
	/**
	 * Refresh  shib user
	 * @access  public
	 * @param   userid
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Valentino Ricci
	 */
	public function RefreshShibUser($userid)
	{		
		$mapped_attributes = $this->getMappedShibAttributes();
		if(count($mapped_attributes)>0)
		{
			/* insert into the db */
			$sql = "UPDATE ".TABLE_PREFIX."users SET ";
			
			foreach ($mapped_attributes as $key => $value)
			{
				$sql = $sql.$key." = ".$value.",";
			}
			//remove last comma
			$sql = substr($sql, 0, -1);
			
			$sql = $sql." WHERE user_id=".$userid;
                        
			if (!$this->execute($sql))
			{
				$msg->addError('DB_NOT_UPDATED');
				return false;
			}
			return true;
		}
	}
	
	public function getMappedShibAttributes()
	{
		global $_shib_acontent_attribute_map;
		
		$shib_variables=array_intersect_key($_SERVER,$_shib_acontent_attribute_map);
		$mapped_attributes = array();
		foreach ($shib_variables as $key => $value)
		{
			$mapped_attributes[$_shib_acontent_attribute_map[$key]] = "'".$value."'";
		}
		return $mapped_attributes;
	}
	
	public function fillUSerFields($userID)
	{
                global $profile_user;
                global $_shib_acontent_attribute_map;
                
                $login_name= substr($_SERVER['eppn'],0, strpos($_SERVER['eppn'],'@'));
                $profile_user['login']= $login_name ;
                
                $sql = "UPDATE ".TABLE_PREFIX."users SET ";
			
			foreach ($profile_user as $key => $value)
			{
                            
                            $sql = $sql.$key.'=\''.$value.'\',';
                            
			}
			//remove last comma
			$sql = substr($sql, 0, -1);
			
			$sql = $sql." WHERE user_id=".$userID;
                                                
			if (!$this->execute($sql))
			{
				$msg->addError('DB_NOT_UPDATED');
				return false;
			}
        
            return true;            
        }
		  
        
        public function isUserFieldsMissing($userID)
	{
		$user = $this->getUserByID($userID);                                  
                if (!$user['login'])
                    {
                    $login_name= substr($_SERVER['eppn'],0, strpos($_SERVER['eppn'],'@'));
                    $user['login'] = $login_name ;
                    }
                                    
		if($this->isFieldsValid($userID, $user['user_group_id'], $user['login'], $user['email'], $user['first_name'], $user['last_name'],
		                        $user['is_author'], $user['organization'], $user['phone'], $user['address'], $user['city'],
	                            $user['province'], $user['country'], $user['postal_code']))
			return false;
		else
			return true;
	}
	
}
?>
