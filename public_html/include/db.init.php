<?php
@session_start();
# This file maps the 'standard' procedures to the FileMaker queries.
# All 'standard' functions begin with db_ and are intended to abstract requests for data from the database engine
# Functions beginning with fm_ are specific to FileMaker, and should never be called from the main site pages. These functions are called only from a db_ function.
# This will simplify porting the system to another database engine, by removing any proprietary statements from the main system logic.
#
# All database queries should return a result array using the convention $array[name]=value. Field names should be generalized to their meaning, so that the underlying field names are unimportant.
# If a database query results in an error, it should return a text string describing the error. This can be a human-readable message, the error text returned by the database, or a combination of the two as appropriate.

# Additional files required for FM integration
require_once('fmapi/FileMaker.php');
require_once( 'shared.php' );

#A few useful constatnts
define( 'APP_DEBUG' , true );
define('APP_TITLE' , 'Androvia Labs');

# Connection variables; may require changing for production environment
define( 'DB_HOST', 'fmdev.antidotesolutions.com' );
//define( 'DB_HOST', 'server.local' );
//define( 'DB_HOST', '127.0.0.1' );
define( 'DB_NAME' ,'Androvia_lim.fmp12' );
define( 'DB_USER' , 'labweb' );
define( 'DB_PASS' , 'Androvia!Lab8' );

# DB_AUTH_METHOD has two acceptable values: table or account
# DB_AUTH_METHOD=account will authenticate against a FileMaker user account (internal or external).
# If this authentication method is used, the account must belong to a Privilege Set with Access via PHP Web Publishing enabled.
//define( 'DB_AUTH_METHOD', 'account' );

# DB_AUTH_METHOD=table will authenticate against a username and password stored in a FileMaker table
# If this authentication method is used, the values for DB_AUTH_LAYOUT, DB_AUTH_USER_FIELD, and DB_AUTH_PASS_FIELD must contain the table and field names used for authentication.
# Also, DB_AUTH_ACTIVE_FIELD is an optional field to verify whether an account is enabled. DB_AUTH_ACTIVE_VALUE is the value DB_AUTH_ACTIVE_FIELD must contain.
define( 'DB_AUTH_METHOD', 'table' );
define( 'DB_AUTH_LAYOUT', 'php_PHY' );
//define( 'DB_AUTH_LAYOUT', 'php_login' );
define( 'DB_AUTH_USER_FIELD', 'Email_t' );
define( 'DB_AUTH_PASS_FIELD', 'WebPassword_t' );

# These are the layout names for queries
define( 'LAY_LOGIN', 'php_intive' );


#
# Core database functions
#

# db_connect() is a standard method that branches depending on the value of DB_AUTH_METHOD
# This creates the actual connection object we'll reference to interact with FileMaker
function db_connect()
{
	# Make sure our $_SESSION variables are set and stored.
	//$timeout = check_timeout();
	//if( is_string( $timeout ) ) return $timeout;

	# Call the connection function
	switch( DB_AUTH_METHOD )
	{
		case 'table' :
			return fm_connect_table();
			break;

		case 'account' :
			return fm_connect_account();
			break;

		default :

			return 'Invalid authentication method selected. Please verify the site configuration.';
	}
}

# Validate the user credentials, or redirect to the login page.
function fm_connect_account()
{
	return 'Account-based login is not enabled.';

	# Create the database handle using the stored credentials.
	$db = new FileMaker( DB_NAME, DB_HOST, $_SESSION['user'], decode( $_SESSION['pass'] ) );

	if( FileMaker::isError( $db ) ) return 'Connection error. The database does not appear to be responding.'.( APP_DEBUG ? $result->getMessage() : '' );

	# Make sure the credentials are valid
	$query = $db->newFindCommand( LAY_STAFF_LIST );
	$query->addFindCriterion( 'AccountName', '=='.$_SESSION['user'] );
	$result = $query->execute();

	if( FileMaker::isError( $result ) ) return 'Authentication error. Please check your account and password and try again.'.( APP_DEBUG ? $result->getMessage() : '' );


	# For simplicity later, set the User record ID to the session
	$record = $result->getFirstRecord();
	$_SESSION['user_id'] = $record->getField('StaffID_PKt');


	# We'll return the database object in $db if everything is ok. Otherwise we'll be returning an error string.
	return $db;
}

function fm_connect_table()
{
	# Create the database handle using the default PHP account.
	$db = new FileMaker( DB_NAME , DB_HOST , DB_USER , DB_PASS );

	if( FileMaker::isError( $db ) ) return 'Connection error. The database does not appear to be responding. '.( APP_DEBUG ? $result->getMessage() : '' );

	# Short-circuit if we've already confirmed the credentials and cached the user data
	if( isset( $_SESSION['user_info'] ) ) return $db;

	# Verify the stored credentials against the user table.
	$query = $db->newFindCommand( DB_AUTH_LAYOUT );
	$query->addFindCriterion( DB_AUTH_USER_FIELD , '=='.$_SESSION['user'] );
	$query->addFindCriterion( DB_AUTH_PASS_FIELD , '=='.$_SESSION['pass'] );
	$result = $query->execute();

	if( FileMaker::isError( $result ) ) return 'Authentication error. Please check your email address and password and try again. '.( APP_DEBUG ? $result->getMessage() : '' );

	# Store the Chef info in the $_SESSION
	$record = $result->getFirstRecord();
	$_SESSION['user_info'] = array();
	$_SESSION['user_info']['name_first'] = $record->getField('FirstName_t');
	$_SESSION['user_info']['name_last'] = $record->getField('LastName_t');
	$_SESSION['user_info']['pk'] = $record->getField('__pk_PhysicianID_n');
#	$_SESSION['user_info']['user_phone'] = $record->getField('invt_user::user Phone');
#	$_SESSION['user_info']['email'] = $record->getField('invt_user::user Email');
		
	# Store the invitation info in $_SESSION	
/*	$_SESSION['invite_info'] = array();
	$_SESSION['invite_info']['months'] = $record->getField('MonthsVL_t');
	$_SESSION['invite_info']['Notes'] = $record->getField('Notes');
	$_SESSION['invite_info']['season'] = $record->getField('Season');
	$_SESSION['invite_info']['return_date'] = $record->getField('DateRequestedReturned_d');
	return $db; */
}

/**** Formatting and general-porpoise function ****/

# Build a select element with values from FileMaker
function buildSelect( $recList, $name, $valField, $hrField='', $extras='', $blank='', $current='' )
{
	if( $hrField=='' ) $hrField=$valField;

	$out = '<select name="'.$name.'" '.$extras.'>';

	if( $blank != '' ) $out .= '<option value="" disabled="disabled">'.$blank.'</option>';

	if( count( $recList ) ) {
		foreach( $recList as $rec ) {
			( ( $rec->getField($valField) == $current ) ? $mark = 'selected="selected"' : $mark='' );

			// if there's nothing in the HR field, leave it out
			if( $rec->getField($hrField) != '' ) $out .= '<option value="'.$rec->getField($valField).'" '.$mark.'>'.$rec->getField($hrField).'</option>';
		}
	}

	$out .= '</select>';

	return $out;

}

# return the supplied or current time in a format accepted by FileMaker
function basic_timeStamp( $time='' )
{
	if( $time == '' ) $time = time();

	return date( "m/d/Y h:i:s A", $time );
}

?>