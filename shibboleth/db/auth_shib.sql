update `AC_privileges` SET `link` = 'shibboleth/profile/index.php' where `privilege_id`=10;
update `AC_language_text` set `text` = 'You must <a href=\"shibboleth/login.php\">login</a> to use this section.' where `term` =  'TR_INFOS_INVALID_USER' and `language_code` = 'en';
