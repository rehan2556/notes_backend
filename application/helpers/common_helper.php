<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('generate_UID'))
{
	function generate_UID()
	{
		return do_hash(random_string('md5')."-".time(), 'md5'); // md5 generates 32char
	}
}

if ( ! function_exists('current_date_time'))
{
	function current_date_time()
	{
		return mdate("%Y-%m-%d %H:%i:%s", time());
	}
}

if ( ! function_exists('br2nl'))
{
	function br2nl($string)
	{
		return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
	}
}

if ( ! function_exists('is_valid_phone_number'))
{
	function is_valid_phone_number($phone_number = NULL)
	{
		$_phone_number = trim($phone_number);
		if (!empty($_phone_number) && (preg_match('/^(\+46)(?:[0-9]{9})$/', $_phone_number)))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}

if ( ! function_exists('replace_if_empty'))
{
	function replace_if_empty($value, $replacement = "")
	{
		return (isset($value) && !empty($value)) ? $value : $replacement;
	}
}

// Function for inter-converios between KB, MB, GB for file sizes
if (!function_exists('format_Size_Units'))
{
	function format_Size_Units($bytes)
	{
		if ($bytes >= 1073741824)
		{
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		}
		elseif ($bytes >= 1048576)
		{
			$bytes = number_format($bytes / 1048576, 2) . ' MB';
		}
		elseif ($bytes >= 1024)
		{
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		}
		elseif ($bytes > 1)
		{
			$bytes = $bytes . ' bytes';
		}
		elseif ($bytes == 1)
		{
			$bytes = $bytes . ' byte';
		}
		else
		{
			$bytes = '0 bytes';
		}
		return $bytes;
	}
}

/**
* Compare 2 multi dimensional array.
* @return array
*/

if( ! function_exists('in_array_r'))
{
	function in_array_r($needle, $haystack, $strict = false)
	{
		foreach ($haystack as $item)
		{
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict)))
			{
				return $item;
			}
		}

		return 0;
	}
}

if( ! function_exists('slugify'))
{
	function slugify($string, $replace = array(), $delimiter = '-')
	{
		if (!extension_loaded('iconv'))
		{
			throw new Exception('iconv module not loaded');
		}

		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		if (!empty($replace))
		{
			$clean = str_replace((array) $replace, ' ', $clean);
		}
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower($clean);
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
		$clean = trim($clean, $delimiter);
		return $clean;
	}
}

if(! function_exists('convertToHoursMins'))
{
	function convertToHoursMins($time, $format = '%02d:%02d')
	{
		if ($time < 1)
		{
			return;
		}
		$hours = floor($time / 60);
		$minutes = ($time % 60);
		return sprintf($format, $hours, $minutes);
	}
}

if(! function_exists('number_weeks_in_dates'))
{
	function number_weeks_in_dates($date1, $date2)
	{
		if($date1 > $date2)
		return number_weeks_in_dates($date2, $date1);

		$first = DateTime::createFromFormat('Y-m-d H:i:s', $date1 . ' 00:00:00');
		$second = DateTime::createFromFormat('Y-m-d H:i:s', $date2 . ' 00:00:00');

		return floor($first->diff($second)->days/7);
	}
}

if(! function_exists('number_months_in_dates'))
{
	function number_months_in_dates($date1, $date2)
	{
		if($date1 > $date2)
		return number_months_in_dates($date2, $date1);

		$ts1 = strtotime($date1);
		$ts2 = strtotime($date2);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		return $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
	}
}

if ( ! function_exists('KT_log_action'))
{
	/**
	* Activage Info Logging Interface
	*
	* We use this as a simple mechanism to access the logging
	* class and send messages to be logged Activage Info.
	*
	* @param	array
	* @return	void
	*/
	function KT_cron_job_log_action($log = array())
	{
		static $CI;

		if ($CI === NULL)
		{
			// references cannot be directly assigned to static variables, so we use an array
			$CI = & get_instance();
			$CI->load->library('KT_Log');
		}

		$CI->kt_log->write_KT_cron_job_log($log);
	}
}
