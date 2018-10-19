<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Users\Administrator\Service\HTML;

defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Extended Utility class for the Users component.
 *
 * @since  2.5
 */
class Users
{
	/**
	 * Display an image.
	 *
	 * @param   string  $src  The source of the image
	 *
	 * @return  string  A <img> element if the specified file exists, otherwise, a null string
	 *
	 * @since   2.5
	 * @throws  \Exception
	 */
	public function image($src)
	{
		$src = preg_replace('#[^A-Z0-9\-_\./]#i', '', $src);
		$file = JPATH_SITE . '/' . $src;

		Path::check($file);

		if (!file_exists($file))
		{
			return '';
		}

		return '<img src="' . Uri::root() . $src . '" alt="">';
	}

	/**
	 * Displays an icon to add a note for this user.
	 *
	 * @param   integer  $userId  The user ID
	 *
	 * @return  string  A link to add a note
	 *
	 * @since   2.5
	 */
	public function addNote($userId)
	{
		$title = Text::_('COM_USERS_ADD_NOTE');

		return '<a href="' . Route::_('index.php?option=com_users&task=note.add&u_id=' . (int) $userId)
			. '" class="hasTooltip btn btn-secondary btn-sm" title="' . $title . '"><span class="fa fa-plus" aria-hidden="true">'
			. '</span> ' . $title . '</a>';
	}

	/**
	 * Displays an icon to filter the notes list on this user.
	 *
	 * @param   integer  $count   The number of notes for the user
	 * @param   integer  $userId  The user ID
	 *
	 * @return  string  A link to apply a filter
	 *
	 * @since   2.5
	 */
	public function filterNotes($count, $userId)
	{
		if (empty($count))
		{
			return '';
		}

		$title = Text::_('COM_USERS_FILTER_NOTES');

		return '<a href="' . Route::_('index.php?option=com_users&view=notes&filter[search]=uid:' . (int) $userId)
			. '" class="dropdown-item"><span class="fa fa-list" aria-hidden="true"></span> ' . $title . '</a>';
	}

	/**
	 * Displays a note icon.
	 *
	 * @param   integer  $count   The number of notes for the user
	 * @param   integer  $userId  The user ID
	 *
	 * @return  string  A link to a modal window with the user notes
	 *
	 * @since   2.5
	 */
	public function notes($count, $userId)
	{
		if (empty($count))
		{
			return '';
		}

		$title = Text::plural('COM_USERS_N_USER_NOTES', $count);

		return '<a href="#userModal_' . (int) $userId . '" id="modal-' . (int) $userId
			. '" data-toggle="modal" class="dropdown-item"><span class="fa fa-eye" aria-hidden="true"></span> ' . $title . '</a>';
	}

	/**
	 * Renders the modal html.
	 *
	 * @param   integer  $count   The number of notes for the user
	 * @param   integer  $userId  The user ID
	 *
	 * @return  string   The html for the rendered modal
	 *
	 * @since   3.4.1
	 */
	public function notesModal($count, $userId)
	{
		if (empty($count))
		{
			return '';
		}

		$title = Text::plural('COM_USERS_N_USER_NOTES', $count);
		$footer = '<button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">'
			. Text::_('JTOOLBAR_CLOSE') . '</button>';

		return HTMLHelper::_(
			'bootstrap.renderModal',
			'userModal_' . (int) $userId,
			array(
				'title'       => $title,
				'backdrop'    => 'static',
				'keyboard'    => true,
				'closeButton' => true,
				'footer'      => $footer,
				'url'         => Route::_('index.php?option=com_users&view=notes&tmpl=component&layout=modal&filter[user_id]=' . (int) $userId),
				'height'      => '300px',
				'width'       => '800px',
			)
		);

	}

	/**
	 * Build an array of block/unblock user states to be used by jgrid.state,
	 * State options will be different for any user
	 * and for currently logged in user
	 *
	 * @param   boolean  $self  True if state array is for currently logged in user
	 *
	 * @return  array  a list of possible states to display
	 *
	 * @since  3.0
	 */
	public function blockStates( $self = false)
	{
		if ($self)
		{
			$states = array(
				1 => array(
					'task'           => 'unblock',
					'text'           => '',
					'active_title'   => 'COM_USERS_USER_FIELD_BLOCK_DESC',
					'inactive_title' => '',
					'tip'            => true,
					'active_class'   => 'unpublish',
					'inactive_class' => 'unpublish',
				),
				0 => array(
					'task'           => 'block',
					'text'           => '',
					'active_title'   => '',
					'inactive_title' => 'COM_USERS_USERS_ERROR_CANNOT_BLOCK_SELF',
					'tip'            => true,
					'active_class'   => 'publish',
					'inactive_class' => 'publish',
				)
			);
		}
		else
		{
			$states = array(
				1 => array(
					'task'           => 'unblock',
					'text'           => '',
					'active_title'   => 'COM_USERS_TOOLBAR_UNBLOCK',
					'inactive_title' => '',
					'tip'            => true,
					'active_class'   => 'unpublish',
					'inactive_class' => 'unpublish',
				),
				0 => array(
					'task'           => 'block',
					'text'           => '',
					'active_title'   => 'COM_USERS_USER_FIELD_BLOCK_DESC',
					'inactive_title' => '',
					'tip'            => true,
					'active_class'   => 'publish',
					'inactive_class' => 'publish',
				)
			);
		}

		return $states;
	}

	/**
	 * Build an array of activate states to be used by jgrid.state,
	 *
	 * @return  array  a list of possible states to display
	 *
	 * @since  3.0
	 */
	public function activateStates()
	{
		$states = array(
			1 => array(
				'task'           => 'activate',
				'text'           => '',
				'active_title'   => 'COM_USERS_TOOLBAR_ACTIVATE',
				'inactive_title' => '',
				'tip'            => true,
				'active_class'   => 'unpublish',
				'inactive_class' => 'unpublish',
			),
			0 => array(
				'task'           => '',
				'text'           => '',
				'active_title'   => '',
				'inactive_title' => 'COM_USERS_ACTIVATED',
				'tip'            => true,
				'active_class'   => 'publish',
				'inactive_class' => 'publish',
			)
		);

		return $states;
	}

	/**
	 * Get the sanitized value
	 *
	 * @param   mixed  $value  Value of the field
	 *
	 * @return  mixed  String/void
	 *
	 * @since   1.6
	 */
	public function value($value)
	{
		if (is_string($value))
		{
			$value = trim($value);
		}

		if (empty($value))
		{
			return Text::_('COM_USERS_PROFILE_VALUE_NOT_FOUND');
		}

		elseif (!is_array($value))
		{
			return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
		}
	}

	/**
	 * Get the space symbol
	 *
	 * @param   mixed  $value  Value of the field
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	public function spacer($value)
	{
		return '';
	}

	/**
	 * Get the sanitized helpsite link
	 *
	 * @param   mixed  $value  Value of the field
	 *
	 * @return  mixed  String/void
	 *
	 * @since   1.6
	 */
	public function helpsite($value)
	{
		if (empty($value))
		{
			return static::value($value);
		}

		$text = $value;

		if ($xml = simplexml_load_file(JPATH_ADMINISTRATOR . '/help/helpsites.xml'))
		{
			foreach ($xml->sites->site as $site)
			{
				if ((string) $site->attributes()->url == $value)
				{
					$text = (string) $site;
					break;
				}
			}
		}

		$value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');

		if (strpos($value, 'http') === 0)
		{
			return '<a href="' . $value . '">' . $text . '</a>';
		}

		return '<a href="http://' . $value . '">' . $text . '</a>';
	}

	/**
	 * Get the sanitized template style
	 *
	 * @param   mixed  $value  Value of the field
	 *
	 * @return  mixed  String/void
	 *
	 * @since   1.6
	 */
	public function templatestyle($value)
	{
		if (empty($value))
		{
			return static::value($value);
		}
		else
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('title')
				->from('#__template_styles')
				->where('id = ' . $db->quote($value));
			$db->setQuery($query);
			$title = $db->loadResult();

			if ($title)
			{
				return htmlspecialchars($title, ENT_COMPAT, 'UTF-8');
			}
			else
			{
				return static::value('');
			}
		}
	}

	/**
	 * Get the sanitized language
	 *
	 * @param   mixed  $value  Value of the field
	 *
	 * @return  mixed  String/void
	 *
	 * @since   1.6
	 */
	public function admin_language($value)
	{
		if (empty($value))
		{
			return static::value($value);
		}
		else
		{
			$file = LanguageHelper::getLanguagePath(JPATH_ADMINISTRATOR, $value) . '/' . $value . '.xml';

			$result = null;

			if (is_file($file))
			{
				$result = LanguageHelper::parseXMLLanguageFile($file);
			}

			if ($result)
			{
				return htmlspecialchars($result['name'], ENT_COMPAT, 'UTF-8');
			}
			else
			{
				return static::value('');
			}
		}
	}

	/**
	 * Get the sanitized language
	 *
	 * @param   mixed  $value  Value of the field
	 *
	 * @return  mixed  String/void
	 *
	 * @since   1.6
	 */
	public function language($value)
	{
		if (empty($value))
		{
			return static::value($value);
		}
		else
		{
			$file = LanguageHelper::getLanguagePath(JPATH_SITE, $value) . '/' . $value . '.xml';

			$result = null;

			if (is_file($file))
			{
				$result = LanguageHelper::parseXMLLanguageFile($file);
			}

			if ($result)
			{
				return htmlspecialchars($result['name'], ENT_COMPAT, 'UTF-8');
			}
			else
			{
				return static::value('');
			}
		}
	}

	/**
	 * Get the sanitized editor name
	 *
	 * @param   mixed  $value  Value of the field
	 *
	 * @return  mixed  String/void
	 *
	 * @since   1.6
	 */
	public function editor($value)
	{
		if (empty($value))
		{
			return static::value($value);
		}
		else
		{
			$db = Factory::getDbo();
			$lang = Factory::getLanguage();
			$query = $db->getQuery(true)
				->select('name')
				->from('#__extensions')
				->where('element = ' . $db->quote($value))
				->where('folder = ' . $db->quote('editors'));
			$db->setQuery($query);
			$title = $db->loadResult();

			if ($title)
			{
				$lang->load("plg_editors_$value.sys", JPATH_ADMINISTRATOR, null, false, true)
				|| $lang->load("plg_editors_$value.sys", JPATH_PLUGINS . '/editors/' . $value, null, false, true);
				$lang->load($title . '.sys');

				return Text::_($title);
			}
			else
			{
				return static::value('');
			}
		}
	}
}
