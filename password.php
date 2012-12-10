<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.password
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('JPATH_BASE') or die;

/**
 * Password User plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  User.password
 * @since       3.0
 */
class PlgUserPassword extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @access  protected
	 * @since   3.0
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		JFormHelper::addFieldPath(__DIR__ . '/fields');
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array  $user     Holds the user data
	 * @param   array  $options  Array holding options (remember, autoregister, group)
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.0
	 */
	public function onUserLogin($user, $options = array())
	{
		// Initialiase variables.
		$instance = $this->_getUser($user, $options);

		// If _getUser returned an error, then pass it back.
		if ($instance instanceof Exception)
		{
			return false;
		}

		// Mark the user as logged in
		$instance->set('guest', 0);

		// Register the needed session variables
		$session = JFactory::getSession();
		$session->set('user', $instance);

		// Check if user not logged in, if yes redirect to change the password.
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();

		if ($instance->get('lastvisitDate') == $db->getNullDate())
		{
			JError::raiseWarning(401, JText::_('PLG_USER_PASSWORD_MSG_CHANGE_PASSWORD'));
			$app->redirect(JRoute::_('index.php?option=com_users&view=profile&layout=edit', false));
		}

		return true;
	}

	/**
	 * This method will return a user object
	 *
	 * If options['autoregister'] is true, if the user doesn't exist yet he will be created
	 *
	 * @param   array  $user     Holds the user data.
	 * @param   array  $options  Array holding options (remember, autoregister, group).
	 *
	 * @return  object  A JUser object.
	 *
	 * @since   3.0
	 */
	protected function _getUser($user, $options = array())
	{
		// Initialiase variables.
		$instance = JUser::getInstance();
		$id       = (int) JUserHelper::getUserId($user['username']);

		if ($id)
		{
			$instance->load($id);
			return $instance;
		}

		return false;
	}
}
