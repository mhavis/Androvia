<?php
#
# Functions shared by the all pages. Independent of the selected database.
#

@session_start();


#
# Formatting stuff
#

# money_format is not defined on Windows (and others) so we need an alternative
function money_formatalt( $number , $currency='' )
{
	if( $number == '' ) return $currency.'0.00';
	if( is_string( $number ) ) $number = filter_var( $number , FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
	return $currency.(number_format( $number , 2 ) );
}

# handle special characters like "smart" quotes that are pretty stupid
function clean_smarts( $s )
{
	$search = array("â€™");
	$replace = array("'");

	return str_replace( $search , $replace , $s );
}

# pvd() is a pre-formatted variable dump. Basically it structures the var_dump function to make it easier to read in a browser.
function pvd( $s )
{
	echo '<pre>';
	var_dump( $s );
	echo '</pre>';
}

/*
function check_timeout()
{
//	if( !isset( $_SESSION['user'] ) || !isset( $_SESSION['pass'] ) || ( $_SESSION['user'] == '' ) || ( $_SESSION['pass'] == '' ) ) return 'An account name and password are required for access.';
	if( !isset( $_SESSION['user'] ) || !isset( $_SESSION['pass'] ) || ( $_SESSION['user'] == '' ) || ( $_SESSION['pass'] == '' ) ) return '';

	# If our session time has expired, log the user out.
	if( isset( $_SESSION['timer'] ) && ( (time() - $_SESSION['timer'] ) > APP_TIMEOUT_SECONDS ) )
	{
		unset( $_SESSION['timer'] );
		return 'Your session has expired. Please log in to continue.';
	}

	$_SESSION['timer'] = time();
	return true;
}
*/
#
# Encode/decode strings for use in storing session passwords
#

# function to encrypt a string
function encode( $str )
{
  for($i=0; $i<5;$i++)
  {
    $str=strrev(base64_encode($str)); //apply base64 first and then reverse the string
  }
  return $str;
}

# function to decrypt the string
function decode( $str )
{
  for($i=0; $i<5;$i++)
  {
    $str=base64_decode(strrev($str)); //apply base64 first and then reverse the string}
  }
  return $str;
}

#
# Shared formatting functions
#

# Return a / delimited date with a 2-digit year
function format_date_short( $dt )
{
	return date( 'n/j/y' , strtotime( $dt ) );
}

function format_date_weekday ( $dt )
{
	return date( 'l' , strtotime( $dt ) );
}

function format_time_short( $tm )
{
	return date( 'g:i A' , strtotime( $tm ) );
}

#
# Form elements
#

# Build a basic select element with a supplied list of values
function list_select( $name, $val_arr, $current='' )
{
	$list = '<select name="'.$name.'">';
	foreach( $val_arr as $value ) {
		$list .= '<option value="'.$value.'">'.$value.'</option>';
	}
	$list .= '</select>';

	return $list;
}

# Convert the supplied array into <radio> objects within a table
# first parameter is the name of the <option> block
# second parameter is the array of options
# optional third parameter is a string representing the selected value
# optional fourth parameter can return the buttons in multiple columns
# optional fifth parameter is a JavaScript callback when clicked
function table_radio( $name, $val_arr, $current='', $cols=1, $js='' )
{
	# note that each $val_arr element is also an array with id/text elements
	# note that $current can be an array of multiple selected values

	# Loop the value array and create the checkboxes
	$out = '<table class="checkboxes">';
	$out .= '<tr>';

	$i=0;
	foreach( $val_arr as $field=>$value )
	{
		$selected = '';
		if( $value == $current ) $selected = ' checked="checked"';

		$out .= '<td><input name="'.$name.'" value="'.$value.'" type="radio"'.$selected.' '.$js.' /></td>';
		$out .= '<td>'.$value.'</td>';
		$i++;
		if( $i % $cols == 0 ) $out .= '</tr><tr>';
	}

	$out .= '</tr>';
	$out .= '</table>';

	return $out;
}

# Convert the supplied array into checkboxes within a table
# first parameter is the name of the checkbox field
# second parameter is the array of options
# optional third parameter is a list of selected values
# optional fourth parameter can return the boxes in multiple columns
# optional fifth parameter is a JavaScript callback when clicked
function table_checkbox( $name, $val_arr, $current=array(), $cols=1, $js='' )
{
	# note that each $val_arr element is also an array with id/text elements
	# note that $current can be an array of multiple selected values

	# Loop the value array and create the checkboxes
	$out = '<table class="checkboxes">';
	$out .= '<tr>';

	$i=0;
	foreach( $val_arr as $field=>$value )
	{
		$selected = '';
		if( is_numeric( array_search( $value, $current ) ) ) $selected = ' checked="checked"';

		$out .= '<td><input name="'.$name.'" value="'.$value.'" type="checkbox"'.$selected.' '.$js.' /></td>';
		$out .= '<td>'.$value.'</td>';
		$i++;
		if( $i % $cols == 0 ) $out .= '</tr><tr>';
	}

	$out .= '</tr>';
	$out .= '</table>';

	return $out;
}

# Create first/previous/next/last links for paginated lists
function paginate( $total, $start, $max )
{
	$out = '';
//	$out .= 'Nav: total='.$total.', start='.$start.', max='.$max;

	# The first and previous links are active only if we're past the first offset
	if( $start > 0 )
	{
		$out .= '<a href="?start=">First</a>';
		$out .= '&nbsp;|&nbsp;';
		$out .= '<a href="?start='.max( 0, $start - $max ).'">Previous</a>';
	}

	# The next link is active as long as the last record isn't displayed
	if( $start + $max < $total )
	{
		if( $out != '' ) $out .= '&nbsp;|&nbsp;';

		$out .= '<a href="?start='.min( $total, $start+$max ).'">Next</a>';
		$out .= '&nbsp;|&nbsp;';
		$out .= '<a href="?start='.max( 0, $total - $max ).'">Last</a>';
	}

	return 'Viewing '.($start+1).'-'.min($total,$start+$max).' of '.$total.' records.'."<br />".$out;
}

#
# Timesheets
#

# Format the Timesheet array for the main page portal
function format_timesheets_portal( $arr )
{
	$out = '<table id="time_sheet_portal_table">';
	$out .= '<tr class="header"><th>Date Sent</th><th>Amount</th><th>Date Paid</th></tr>';
// <tr><td><a href="timesheet_detail.php?sheet=6459">4/2/12</a></td><td>440.00</td><td>4/13/12</td></tr>
	foreach( $arr as $sheet )
	{
		$out .= '<tr>';

		$out .= '<td><a href="timesheet_detail.php?sheet='.$sheet['id'].'">';
		if( strlen( $sheet['date_sent'] >0 ) )
		{
			$out .= format_date_short( $sheet['date_sent'] );
		} else {
			$out .= ( $sheet['date_sent'] );
		}
		$out .= '</a></td>';

		$out .= '<td>'.(money_formatalt( $sheet['amount'], '$' ) ).'</td>';

		if( strlen( $sheet['date_paid'] >0 ) )
		{
			$out .= '<td>'.format_date_short( $sheet['date_paid'] ).'</td>';
		} else {
			$out .= '<td>'.( $sheet['date_paid'] ).'</td>';
		}
		$out .= '</tr>';
	}
	$out .= '</table>';

	return $out;
}

#Tom 7/2012
function format_timesheet_header( $arr )  //there can be only 1
{
		$out = '<div class="page_header">Timesheet Detail</div>';
		$out .= '<div style="float:left;">';
		$out .= '<p class="billed-items">Interpreter: <span class="time-sheet-detail">'.$arr['name'].'</span></p>';
		$out .= '<p class="billed-items">Certification: <span class="time-sheet-detail">'.$arr['cert'].'</span></p>';
		$out .= '</div>';

		$out .= '<div style="float:right;width:100%;"';
		$out .= '<p class="billed-items">Billing Dates: <span class="time-sheet-detail">'.$arr['billing_dates'].'</span></p>';
		$out .= '<p class="billed-items">Interpreter Address: <span class="time-sheet-detail" style="display:block;">'.$arr['address'].'</span></p>';

		$out .= '<p class="billed-items">Date Sent: <span class="time-sheet-detail">'.format_date_short($arr['date_sent']).'</span></p>';

		$out .= '<p class="billed-items"><span class="detail-billed-total">Billed Total: '.(money_formatalt( $arr['amount'], '$' ) ).'</span></p>';
		$out .= '</div>';
/*
		$out = '<table id="time_sheet_header_table" border="0">';
		$out .= '<tr>';
		$out .=  '<th align = "left" >Interpreter:</th><td align = "left" width = "250px">'..'</td>';
		$out .=  '<th align = "left">Certification:</th><td align = "left" width = "100px">'.$arr['cert'].'</td>';
		$out .=  '<th align = "left">Date Sent:</th><td align = "left">'.format_date_short($arr['date_sent']).'</td>';
		$out .= '</tr>';
		$out .= '<tr>';
		$out .=  '<th align = "left">Billing Dates:</th><td align = "left" width = "250px">'.$arr['billing_dates'].'</td>';
		$out .=  '<th>&nbsp;</th><td>&nbsp;</td>';
		$out .=  '<th>&nbsp;</th><td>&nbsp;</td>';
		$out .= '</tr>';
		$out .= '<tr>';
		$out .=  '<th align = "left" >Interpreter Address:</th><td align = "left" width = "250px">'.$arr['address'].'</td>';
		$out .=  '<th align = "left">&nbsp;</th><td>&nbsp;</td>';
		$out .=  '<th align = "left">Total:</th>';
		$out .= '<td align = "left">'.(money_formatalt( $arr['amount'], '$' ) ).'</td>';
		$out .= '</tr>';

	$out .= '</table>';
*/
	return $out;
}


function format_timesheet_details( $arr )
{
	$out = '<table id="time_sheet_detail_table">';
	$out .= '<tr><th>Date</th><th>Job #</th><th align = "left">Location</th><th>Start</th><th>End</th><th>Total Hours</th><th>Rate</th><th>Total Billed</th></tr>';
	foreach( $arr as $sheet )
	{
		$out .= '<tr>';
		$out .= '<td align="center" class="nowrap">'.format_date_short( $sheet['date'] ).'</td>';
		$out .= '<td align="center" class="nowrap">'.( $sheet['job'] ).'&nbsp;&nbsp;</td>';
		$out .= '<td align="left">'.( $sheet['location'] ).'</td>';
		$out .= '<td align="center" class="nowrap">'.strtoupper( ( date("g:i a", strtotime($sheet['start'])) ) ).'</td>';
		$out .= '<td align="center" class="nowrap">'.strtoupper( ( date("g:i a", strtotime($sheet['end'])) ) ).'</td>';
		$out .= '<td align="center" class="nowrap">'.( $sheet['hours'] ).'</td>';
		$out .= '<td align="center" class="nowrap">'.( money_formatalt( $sheet['rate'] ,'$' ) ).'</td>';
		$out .= '<td align="center" class="nowrap">'.( money_formatalt( $sheet['paid'] ,'$' ) ).'</td>';
		$out .= '</tr>';
	}
	$out .= '</table>';

	return $out;
}

function format_timesheet_details_unbilled( $arr,$ID )
{
	$out = '<table id="time_sheet_unbilled_table">';
	$out .= '<tr><th></th><th style="text-align:right">Date</th><th style="text-align:center">Job #</th><th style="text-align:left">Location</th><th style="text-align:right">Start</th><th style="text-align:right">End</th><th style="text-align:right">Hours</th><th style="text-align:right">Rate</th><th style="text-align:right">Amount</th></tr>';
	foreach( $arr as $sheet )
	{
		$out .= '<tr>';
		if (strlen($sheet['cancelled'])<1){
		$out .= '<td style="text-align:center">'.'<a href="dotimesheet_add.php?ID='.$sheet['id'].'&TSID='.$ID.'" onclick="this.disabled=true">'.'<img src="images/add.jpg" border=0>'.'</a></td>';
		}else {
		$out .= '<td style="text-align:center">'.'</td>';

		}
		$out .= '<td style="text-align:right" class="nowrap">'.format_date_short( $sheet['date'] ).'</td>';
		$out .= '<td style="text-align:center" class="nowrap">'.( $sheet['job'] ).'&nbsp;&nbsp;</td>';
		$out .= '<td style="text-align:left">'.( $sheet['location'] ).'</td>';
		$out .= '<td style="text-align:right" class="nowrap">'.strtoupper( ( date("g:i a", strtotime($sheet['start'])) ) ).'</td>';
		$out .= '<td style="text-align:right" class="nowrap">'.strtoupper( ( date("g:i a", strtotime($sheet['end'])) ) ).'</td>';
		$out .= '<td style="text-align:right">'.( $sheet['hours'] ).'</td>';
		$out .= '<td style="text-align:right">'.( money_formatalt( $sheet['rate'] , '$' ) ).'</td>';
		$out .= '<td style="text-align:right">'.( money_formatalt( $sheet['paid'] , '$' ) ).'</td>';
		$out .= '</tr>';
	}
	$out .= '</table>';

	return $out;
}

function format_timesheet_details_billed( $arr,$ID, &$total='' )
{
	$out = '<div id="time_sheet_billed">';
	$out .= '<table id="time_sheet_billed_table">';
	$out .= '<tr><th></th><th style="text-align:right">Date</th><th style="text-align:center">Job #</th><th align = "left">Location</th><th style="text-align:right">Start</th><th style="text-align:right">End</th><th style="text-align:right">Hours</th><th style="text-align:right">Rate</th><th style="text-align:right">Amount</th></tr>';
	$total = 0;
	$rowcount = 0;
	foreach( $arr as $sheet )
	{
		$rowcount++;

		$out .= '<tr>';
		if ((strlen($sheet['datePaid'])<1) and (strlen($sheet['amountPaid'])<1) )    {
			$out .= '<td style="text-align:center">'.'<img src="images/remove.jpg" border=0 onclick = "disp_confirm('."'dotimesheet_remove.php?ID=".$sheet['id'].'&TSID='.$ID."'".')">'.'</td>';
		} else {
			$out .= '<td style="text-align:center">'.'</td>';
		}

		$out .= '<td style="text-align:right" class="nowrap">'.format_date_short( $sheet['date'] ).'</td>';
		$out .= '<td style="text-align:center" class="nowrap">'.( $sheet['job'] ).'&nbsp;&nbsp;</td>';
		$out .= '<td style="text-align:left">'.( $sheet['location'] ).'</td>';
		$out .= '<td style="text-align:right" class="nowrap">'.strtoupper( ( date("g:i a", strtotime($sheet['start'])) ) ).'</td>';
		$out .= '<td style="text-align:right" class="nowrap">'.strtoupper( ( date("g:i a", strtotime($sheet['end'])) ) ).'</td>';
		$out .= '<td style="text-align:right">'.( $sheet['hours'] ).'</td>';
		$out .= '<td style="text-align:right">'.(money_formatalt( $sheet['rate'], '$' ) ).'</td>';
		$out .= '<td style="text-align:right">'.(money_formatalt( $sheet['paid'], '$' ) ).'</td>';

		$total = $total + floatval($sheet['paid']);
		$out .= '</tr>';
		$out .= '<tr>';
		$out .= '<td align = left colspan="8">';
		$out .= 'Comment: '.'<input type="text" name="comment'.$rowcount.'" size="40" value="'.($sheet['comment']).'" />';
		$out .= '<input type="hidden" value="'.$sheet['recid'].'" name="recid'.$rowcount.'" />';

		$out .= '</td>';
		$out .= '</tr>';
	}//end for

	$total = money_formatalt( $total , '$' );

//	$out .= '<tr><td colspan = "7">&nbsp;</td><td align = "center">Total:</td>';
//	$out .= '<td style="text-align:right">'.(money_formatalt( $total , '$' ) ).'</td>';
//	$out .= '</td></tr>';

	$out .= '</table>';
	$out .= '</div>';
	$out .= '<input type="hidden" value="'.$rowcount.'" name="rowcount" />';

	$out .= '</form>';
	return $out;
}

function format_time_dates( $arr,$ID )
{
	$out = '<form id="time_form" name="time_form" method="POST" action="dosavesend.php">';
	$out .= '<input type="hidden" value="'.$ID.'" name="ID" />';
	$out .= '<input type="hidden" value="'.$arr['id'].'" name="recid" />';

	$out .= '<table id="time_table">';
	$out .= '<tr><th colspan="4">Date(s) of Work</th></tr>';
	$out .= '<tr><td align = "left">From</td><td align = "left">To</td><td width = "90px"></td><td></td></tr>';
	$out .= '<tr>';
	$out .= '<td align="center">'.'<input type="text" id="ts_date1" name="date1" value="'.($arr['start']).'" onchange="ajax_save_date('.$ID.',this);" />'.'</td>';
	$out .= '<td align="center">'.'<input type="text" id="ts_date2" name="date2" value="'.($arr['end']).'" onchange="ajax_save_date('.$ID.',this);" />'.'</td>';
	$out .= '<td align="right">';
	$out .= '<input id="save" class="blue button" type="submit" value="Save" name = "save" />';
	$out .= '</td>';
	$out .= '<td align="right">';
	$out .= '<input id="send" class="red button" type="submit" value="Send" name = "send" />';
	$out .= '</td>';
	$out .= '</tr>';
	$out .= '</table>';
	//$out .= '</form>';

	return $out;
}

# Format the schedule array for the full list
function format_schedule_list( $arr, $start=0 )
{
	$list_nav = paginate( $arr['total'], $start, APP_LIST_MAX );
	$out = '<table id="schedule_list_table">';
	$out .= '<tr><th colspan="8" class="right">'.$list_nav.'</th></tr>';
	$out .= '<tr class="header"><th>Date</th><th>Start Time</th><th>End Time</th><th>&nbsp;</th><th class="left hidden">Job #</th><th class="left">Location</th></tr>';
	$first = true;

	foreach( $arr['records'] as $appt )
	{
		if( !$first ) $out .= '<tr><td colspan="6"><hr /></td></tr>';
		$first = false;
		$strike_start = '';
		$strike_end = '';

//		if( strcmp( $appt["cancelled"] , "1" ) !=0 )
		if( $appt["cancelled"] == "1" )
		{
			$strike_start = '<strike style="color:#888888">';
			$strike_end = '</strike>';
		}

		$out .= '<tr>';
		$out .= '<td class="right" class="nowrap"><a href="schedule_detail.php?sheet='.$appt['id'].'">'.format_date_short( $appt['date'] ).'<br />'.format_date_weekday( $appt['date'] ).'</a></td>';
		$out .= '<td class="right" class="nowrap">'.$strike_start.format_time_short( $appt['time_start'] ).$strike_end.'</td>';
		$out .= '<td class="right" class="nowrap">'.$strike_start.format_time_short( $appt['time_end'] ).$strike_end.'</td>';
		$out .= '<td>&nbsp;</td>';
		$out .= '<td class="left hidden" class="nowrap">'.$strike_start.$appt['job'].$strike_end.'</td>';
		$out .= '<td class="left">'.$strike_start.$appt['location'].$strike_end.'</td>';
//		$out .= '<td class="left">'.$strike_start.$appt['contact'].$strike_end.'</td>';
//		$out .= '<td class="left">'.$strike_start.$appt['phone'].$strike_end.'</td>';
//		$out .= '<td class="left" class="hidden">'.$strike_start.$appt['subject'].$strike_end.'</td>';
		$out .= '</tr>';
	}
	$out .= '</table>';

	return $out;
}

#TJ 7/2012
function format_cal_details( $arr )
{
	$out='<table id="cal_details_table">';
	$out .= '<tr><th align="left">Job #:</th><td align="left" width="100px">'.$arr['job'].'</td></tr>';
	$out .= '<tr><th align="left">Date:</th><td align="left" width="100px">'.format_date_short($arr['date']).'</td></tr>';
	$out .= '<tr><th align="left">Start:</th><td align="left" width="100px">'.format_time_short($arr['time_start']).'</td></tr>';
	$out .= '<tr><th align="left">End:</th><td align="left" width="100px">'.format_time_short($arr['time_end']).'</td></tr>';
	$out .= '<tr><th align="left">Subject:</th><td align="left" width="100px">'.$arr['subject'].'</td></tr>';
	$out .= '<tr><th align="left">Location:</th><td align="left" width="100px">'.$arr['location'].'</td></tr>';
	$out .= '<tr><th align="left">Directions:</th><td align="left" width="100px">'.($arr['directions']).'</td></tr>';
	$out .= '<tr><th align="left">Contact:</th><td align="left" width="100px">'.$arr['contact'].'</td></tr>';
	$out .= '<tr><th align="left">Phone:</th><td align="left" width="100px">'.$arr['phone'].'</td></tr>';
	$out .= '<tr><th align="left">Consumers:</th><td align="left" width="100px">'.$arr['consumer1'].'</td></tr>';
	$out .= '<tr><th align="left">Cancellation Policy:</th><td align="left">'.($arr['policy']).'</td></tr>';
	if( $arr['consumer2'] != '' ) $out .= '<tr><th align="left">&nbsp;</th><td align="left" width="100px">'.$arr['consumer2'].'</td></tr>';
	if( $arr['consumer3'] != '' ) $out .= '<tr><th align="left">&nbsp;</th><td align="left" width="100px">'.$arr['consumer3'].'</td></tr>';
	$out .= '<tr><th align="left">Notes:</th><td align="left" width="100px">'.$arr['notes'].'</td></tr>';
	$out .= '</table>';

	return $out;
}



#
# User details and preferences
#

# Return the user detail form
function fomat_user_detail( $arr )
{
	# Set up the checkbox tables
	$locations = table_checkbox('locations', $_SESSION['lists']['user']['locations'], $arr['locations'], $cols=4);
	$settings = table_checkbox('settings', $_SESSION['lists']['user']['settings'], $arr['settings'], $cols=3);
	$availability = table_checkbox('availability', $_SESSION['lists']['user']['availability'], $arr['availability'], $cols=6);
	$share = table_radio('share', array('Yes','No'), $arr['share'], $cols=2).'</td>';

	# Capture the output buffer so we can return the included content inline later in the page
	ob_start();
	include( 'elements/contact_info.php' );

	return ob_get_clean();
}

function fomat_user_detail_old( $arr )
{
	# First build the two sides

	# Contact info
	$info = '';
	$info .= '<table id="contact_info_table">';
	$info .= '<tr>';
	$info .= '<th colspan="3">Contact Info</th>';
	$info .= '</tr>';
	$info .= '<tr>';
	$info .= '<td>First name</td>';
	$info .= '<td colspan="2"><input type="text" class="input_text_long" name="name_first" value="'.$arr['name_first'].'" /></td>';
	$info .= '</tr>';
	$info .= '<tr>';
	$info .= '<td>Last name</td>';
	$info .= '<td colspan="2"><input type="text" class="input_text_long" name="name_last" value="'.$arr['name_last'].'" /></td>';
	$info .= '</tr>';
	$info .= '<tr><td colspan="3">&nbsp;</td></tr>';
	$info .= '<tr>';
	$info .= '<td>Address</td>';
	$info .= '<td colspan="2"><input type="text" class="input_text_long" name="address" value="'.$arr['address'].'" /></td>';
	$info .= '</tr>';
	$info .= '<tr>';
	$info .= '<td>City</td>';
	$info .= '<td colspan="2"><input type="text" class="input_text_long" name="city" value="'.$arr['city'].'" /></td>';
	$info .= '</tr>';
	$info .= '<tr>';
	$info .= '<td>State</td>';
	$info .= '<td colspan="2"><input type="text" class="input_text_short" name="city" value="'.$arr['state'].'" /></td>';
	$info .= '</tr>';
	$info .= '<tr>';
	$info .= '<td>ZIP code</td>';
	$info .= '<td colspan="2"><input type="text" class="input_text_short" name="zip" value="'.$arr['zip'].'" /></td>';
	$info .= '</tr>';
	$info .= '<tr><td colspan="3">&nbsp;</td></tr>';
	$info .= '<tr>';
	$info .= '<td>Main phone</td>';
	$info .= '<td><input type="text" class="input_text_std" name="phone1" value="'.$arr['phone1'].'" /></td>';
	$info .= '<td><input type="text" class="input_text_std" name="phone1_type" value="'.$arr['phone1_type'].'" /></td>';
	$info .= '</tr>';
	$info .= '<tr>';
	$info .= '<td>Alt phone</td>';
	$info .= '<td><input type="text" class="input_text_std" name="phone2" value="'.$arr['phone2'].'" /></td>';
	$info .= '<td><input type="text" class="input_text_std" name="phone2_type" value="'.$arr['phone2_type'].'" /></td>';
	$info .= '</tr>';
	$info .= '<tr><td colspan="3">&nbsp;</td></tr>';
	$info .= '<tr>';
	$info .= '<td>Email</td>';
	$info .= '<td colspan="2"><input type="text" class="input_text_long" name="email1" value="'.$arr['email1'].'" /></td>';
	$info .= '</tr>';
	$info .= '<tr>';
	$info .= '<td>Alt email</td>';
	$info .= '<td colspan="2"><input type="text" class="input_text_long" name="email2" value="'.$arr['email2'].'" /></td>';
	$info .= '</tr>';
	$info .= '</table>';

	# User preferences
	$prefs = '';
	$prefs .= '<table id="preferences_table">';
	$prefs .= '<tr>';
	$prefs .= '<th colspan="2">Preferences</th>';
	$prefs .= '</tr>';
	$prefs .= '<tr>';
	$prefs .= '<th colspan="2">Locations</th>';
	$prefs .= '</tr><tr>';
	$prefs .= '<td colspan="2">'.table_checkbox('settings', $_SESSION['lists']['user']['locations'], $arr['locations'], $cols=4).'</td>';
	$prefs .= '</tr>';
	$prefs .= '<tr><td colspan="2">&nbsp;</td></tr>';
	$prefs .= '<tr>';
	$prefs .= '<th colspan="2">Settings</th>';
	$prefs .= '</tr><tr>';
	$prefs .= '<td colspan="2">'.table_checkbox('settings', $_SESSION['lists']['user']['settings'], $arr['settings'], $cols=3).'</td>';
	$prefs .= '</tr>';
	$prefs .= '<tr><td colspan="2">&nbsp;</td></tr>';
	$prefs .= '<tr>';
	$prefs .= '<th colspan="2">Availability</th>';
	$prefs .= '</tr><tr>';
	$prefs .= '<td colspan="2">'.table_checkbox('availability', $_SESSION['lists']['user']['availability'], $arr['availability'], $cols=6).'</td>';
	$prefs .= '</tr>';
	$prefs .= '<tr>';
	$prefs .= '<td>'.table_radio('share', array('Yes','No'), $arr['share'], $cols=2).'</td>';
	$prefs .= '<td>May we share your contact information with team interpreters?</td>';
	$prefs .= '</tr>';
	$prefs .= '</table>';

	# Bind the sides into the output table
	$out = '';
	$out .= '<table id="user_detail_table">';
	$out .= '<tr>';
	$out .= '<td width="50%" class="top">'.$info.'</td>';
	$out .= '<td width="50%" class="top">'.$prefs.'</td>';
	$out .= '</tr>';
	$out .= '<tr>';
	$out .= '<td colspan="2" class="right"><div id="save_user" onclick="save_user()">Save</div></td>';
	$out .= '</tr>';
	$out .= '</table>';

	return $out;
}

# Formaat the Address Book
# Format the schedule array for the full list
function format_contact_list( $arr, $start=0 )
{
	$list_nav = paginate( $arr['total'], $start, APP_LIST_MAX );
	$out = '<table id="contact_list_table">';
	$out .= '<tr><th colspan="8" class="right">'.$list_nav.'</th></tr>';
	$out .= '<tr class="header"><th>Name</th><th>Email</th><th>Phone</th></tr>';

	foreach( $arr['records'] as $contact )
	{
		$out .= '<tr>';
		$out .= '<td class="right">'.$contact['name_full'].'</td>';
		$out .= '<td class="right">'.$contact['email'].'</td>';
		$out .= '<td class="right">'.$contact['phone'].'</td>';
		$out .= '</tr>';
	}
	$out .= '</table>';

	return $out;
}


?>