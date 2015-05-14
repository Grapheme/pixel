<?php
/**
 * @brief		4.0.0 Alpha 1 Upgrade Code
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @subpackage	Calendar
 * @package		IPS Social Suite
 * @since		8 Jan 2014
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\calendar\setup\upg_40000;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * 4.0.0 Alpha 1 Upgrade Code
 */
class _Upgrade
{
	/**
	 * Upgrade calendar
	 *
	 * @return	array	If returns TRUE, upgrader will proceed to next step. If it returns any other value, it will set this as the value of the 'extra' GET parameter and rerun this step (useful for loops)
	 */
	public function step1()
	{
		\IPS\Db::i()->update( 'calendar_events', array( 'event_recurring' => NULL ), array( 'event_recurring=?', '0' ) );
		\IPS\Db::i()->update( 'calendar_events', array( 'event_recurring' => "FREQ=yearly;INTERVAL=1" ), array( 'event_recurring=?', '3' ) );
		\IPS\Db::i()->update( 'calendar_events', array( 'event_recurring' => "FREQ=monthly;INTERVAL=1" ), array( 'event_recurring=?', '2' ) );
		\IPS\Db::i()->update( 'calendar_events', array( 'event_recurring' => "FREQ=weekly;INTERVAL=1" ), array( 'event_recurring=?', '1' ) );

		\IPS\Db::i()->update( 'calendar_events', "event_recurring=CONCAT( event_recurring, ';UNTIL=', DATE_FORMAT( event_end_date, '%Y%m%dT%k%i%sZ' ) ), event_end_date=NULL", 'event_recurring IS NOT NULL AND event_end_date IS NOT NULL' );

		\IPS\Db::i()->update( 'calendar_events', array( 'event_approved' => 0 ), "event_private=1 or event_perms!='*'" );
		\IPS\Db::i()->update( 'calendar_events', array( 'event_end_date' => NULL ), array( 'event_end_date=?', '0000-00-00 00:00:00' ) );

		\IPS\Db::i()->dropColumn( 'calendar_events', array( 'event_private', 'event_perms' ) );

		return true;
	}

	/**
	 * Custom title for this step
	 *
	 * @return string
	 */
	public function step1CustomTitle()
	{
		return "Upgrading calendar events";
	}

	/**
	 * Upgrade calendar
	 *
	 * @return	array	If returns TRUE, upgrader will proceed to next step. If it returns any other value, it will set this as the value of the 'extra' GET parameter and rerun this step (useful for loops)
	 */
	public function step2()
	{
		foreach( \IPS\Db::i()->select( '*', 'calendar_calendars' ) as $calendar )
		{
			\IPS\Lang::saveCustom( 'calendar', "calendar_calendar_{$calendar['cal_id']}", html_entity_decode( $calendar['cal_title'], ENT_QUOTES, 'UTF-8' ) );
		}

		\IPS\Db::i()->dropColumn( 'calendar_calendars', 'cal_title' );

		return true;
	}

	/**
	 * Custom title for this step
	 *
	 * @return string
	 */
	public function step2CustomTitle()
	{
		return "Upgrading calendars";
	}
}