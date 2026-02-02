<?php

namespace tws\widgets\datetimepicker;

/**
 * FormatConverter provides functionality to convert between different formatting pattern formats.
 *
 * It provides functions to convert date format patterns between different conventions.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @author Enrica Ruedin <e.ruedin@guggach.com>
 * @author Alin Hort <alinhort@gmail.com>
 */
class MomentFormat
{
	/**
	 * Converts a date format pattern from [ICU format][] to [MomentJs format][].
	 *
	 * The conversion is limited to date patterns that do not use escaped characters.
	 * Patterns like `d 'of' MMMM yyyy` which will result in a date like `1 of December 2014` may not be converted correctly
	 * because of the use of escaped characters.
	 *
	 * Pattern constructs that are not supported by the PHP format will be removed.
	 *
	 * [ICU format]: http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
	 * [MomentJs format]: https://momentjs.com/docs/#/displaying/
	 *
	 * @param string $pattern date format pattern in ICU format.
	 * @param string $type 'date', 'time', or 'datetime'.
	 * @param string $locale the locale to use for converting ICU short patterns `short`, `medium`, `long` and `full`.
	 * If not given, `Yii::$app->language` will be used.
	 * @return string The converted date format pattern.
	 */
	public static function convertDateIcuToMoment($pattern, $type = 'date', $locale = null)
	{
		// http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
		// escaped text
		$escaped = [];
		if (preg_match_all('/(?<!\')\'(.*?[^\'])\'(?!\')/', $pattern, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$match[1] = str_replace('\'\'', '\'', $match[1]);
				$escaped[$match[0]] = '\\' . implode('\\', preg_split('//u', $match[1], -1, PREG_SPLIT_NO_EMPTY));
			}
		}

		return strtr($pattern, array_merge($escaped, [
			'G' => '',      		// era designator like (Anno Domini)
			'Y' => 'Y',     		// 4digit year of "Week of Year"
			'y' => '',    			// 4digit year e.g. 2014
			'yyyy' => 'YYYY', 	// 4digit year e.g. 2014
			'yy' => 'YY',    		// 2digit year number eg. 14
			'u' => '',      		// extended year e.g. 4601
			'U' => '',      		// cyclic year name, as in Chinese lunar calendar
			'r' => '',      		// related Gregorian year e.g. 1996
			'Q' => 'Q',      		// number of quarter
			'QQ' => '',     		// number of quarter '02'
			'QQQ' => '',    		// quarter 'Q2'
			'QQQQ' => 'Qo',   	// quarter '2nd quarter'
			'QQQQQ' => '',  		// number of quarter '2'
			'q' => '',      		// number of Stand Alone quarter
			'qq' => '',     		// number of Stand Alone quarter '02'
			'qqq' => '',    		// Stand Alone quarter 'Q2'
			'qqqq' => '',   		// Stand Alone quarter '2nd quarter'
			'qqqqq' => '',  		// number of Stand Alone quarter '2'
			'M' => 'M',     		// Numeric representation of a month, without leading zeros
			'MM' => 'MM',   		// Numeric representation of a month, with leading zeros
			'MMM' => 'MMM',  		// A short textual representation of a month, three letters
			'MMMM' => 'MMMM', 	// A full textual representation of a month, such as January or March
			'MMMMM' => '',
			'L' => '',     			// Stand alone month in year
			'LL' => '',   			// Stand alone month in year
			'LLL' => '',   			// Stand alone month in year
			'LLLL' => '', 			// Stand alone month in year
			'LLLLL' => '',  		// Stand alone month in year
			'w' => 'w',      		// ISO-8601 week number of year
			'ww' => 'ww',     	// ISO-8601 week number of year
			'W' => '',      		// week of the current month
			'd' => 'D',     		// day without leading zeros
			'dd' => 'DD',   		// day with leading zeros
			'D' => 'DDD',     	// day of the year 0 to 365
			'F' => 'Do',      	// Day of Week in Month. eg. 2nd Wednesday in July
			'g' => '',      		// Modified Julian day. This is different from the conventional Julian day number in two regards.
			'E' => 'ddd',     	// day of week written in short form eg. Sun
			'EE' => 'ddd',
			'EEE' => 'ddd',
			'EEEE' => 'dddd', 	// day of week fully written eg. Sunday
			'EEEEE' => '',
			'EEEEEE' => 'dd',
			'e' => 'E',      		// ISO-8601 numeric representation of the day of the week 1=Mon to 7=Sun
			'ee' => '',     		// php 'w' 0=Sun to 6=Sat isn't supported by ICU -> 'w' means week number of year
			'eee' => 'ddd',
			'eeee' => 'dddd',
			'eeeee' => '',
			'eeeeee' => 'dd',
			'c' => 'E',      		// ISO-8601 numeric representation of the day of the week 1=Mon to 7=Sun
			'cc' => 'E',     		// php 'w' 0=Sun to 6=Sat isn't supported by ICU -> 'w' means week number of year
			'ccc' => 'ddd',
			'cccc' => 'dddd',
			'ccccc' => '',
			'cccccc' => 'dd',
			'a' => 'a',      		// am/pm marker
			'h' => 'h',      		// 12-hour format of an hour without leading zeros 1 to 12h
			'hh' => 'hh',     	// 12-hour format of an hour with leading zeros, 01 to 12 h
			'H' => 'H',      		// 24-hour format of an hour without leading zeros 0 to 23h
			'HH' => 'HH',     	// 24-hour format of an hour with leading zeros, 00 to 23 h
			'k' => 'k',      		// hour in day (1~24)
			'kk' => 'kk',     	// hour in day (1~24)
			'K' => '',      		// hour in am/pm (0~11)
			'KK' => '',     		// hour in am/pm (0~11)
			'm' => 'm',      		// Minutes without leading zeros, not supported by php but we fallback
			'mm' => 'mm',     	// Minutes with leading zeros
			's' => 's',      		// Seconds, without leading zeros, not supported by php but we fallback
			'ss' => 'ss',     	// Seconds, with leading zeros
			'S' => 'S',      		// fractional second
			'SS' => 'SS',     	// fractional second
			'SSS' => 'SSS',    	// fractional second
			'SSSS' => 'SSSS',   // fractional second
			'A' => '',      		// milliseconds in day
			'z' => 'z',      		// Timezone abbreviation
			'zz' => 'zz',     	// Timezone abbreviation
			'zzz' => '',    		// Timezone abbreviation
			'zzzz' => '',   		// Timezone full name, not supported by php but we fallback
			'Z' => 'Z',      		// Difference to Greenwich time (GMT) in hours
			'ZZ' => 'ZZ',     	// Difference to Greenwich time (GMT) in hours
			'ZZZ' => '',    		// Difference to Greenwich time (GMT) in hours
			'ZZZZ' => '',   		// Time Zone: long localized GMT (=OOOO) e.g. GMT-08:00
			'ZZZZZ' => '',  		// Time Zone: ISO8601 extended hms? (=XXXXX)
			'O' => '',      		// Time Zone: short localized GMT e.g. GMT-8
			'OOOO' => '',   		// Time Zone: long localized GMT (=ZZZZ) e.g. GMT-08:00
			'v' => '',      		// Time Zone: generic non-location (falls back first to VVVV and then to OOOO) using the ICU defined fallback here
			'vvvv' => '',   		// Time Zone: generic non-location (falls back first to VVVV and then to OOOO) using the ICU defined fallback here
			'V' => '',      		// Time Zone: short time zone ID
			'VV' => '',     		// Time Zone: long time zone ID
			'VVV' => '',    		// Time Zone: time zone exemplar city
			'VVVV' => '',   		// Time Zone: generic location (falls back to OOOO) using the ICU defined fallback here
			'X' => '',      		// Time Zone: ISO8601 basic hm?, with Z for 0, e.g. -08, +0530, Z
			'XX' => '',     		// Time Zone: ISO8601 basic hm, with Z, e.g. -0800, Z
			'XXX' => '',    		// Time Zone: ISO8601 extended hm, with Z, e.g. -08:00, Z
			'XXXX' => '',   		// Time Zone: ISO8601 basic hms?, with Z, e.g. -0800, -075258, Z
			'XXXXX' => '',  		// Time Zone: ISO8601 extended hms?, with Z, e.g. -08:00, -07:52:58, Z
			'x' => '',      		// Time Zone: ISO8601 basic hm?, without Z for 0, e.g. -08, +0530
			'xx' => '',     		// Time Zone: ISO8601 basic hm, without Z, e.g. -0800
			'xxx' => '',    		// Time Zone: ISO8601 extended hm, without Z, e.g. -08:00
			'xxxx' => '',   		// Time Zone: ISO8601 basic hms?, without Z, e.g. -0800, -075258
			'xxxxx' => '',  		// Time Zone: ISO8601 extended hms?, without Z, e.g. -08:00, -07:52:58
		]));
	}

	/**
	 * Converts a date format pattern from [php date() function format][] to [MomentJs format][].
	 *
	 * Pattern constructs that are not supported by the ICU format will be removed.
	 *
	 * [PHP date format]: http://php.net/manual/en/function.date.php
	 * [MomentJs format]: https://momentjs.com/docs/#/displaying/
	 *
	 * @param string $pattern date format pattern in php date()-function format.
	 * @return string The converted date format pattern.
	 */
	public static function convertDatePhpToMoment($pattern)
	{
		return strtr($pattern, [
			// Day
			'd' => 'DD',    // Day of the month, 2 digits with leading zeros 	01 to 31
			'D' => 'ddd',   // A textual representation of a day, three letters 	Mon through Sun
			'j' => 'D',     // Day of the month without leading zeros 	1 to 31
			'l' => 'dddd',  // A full textual representation of the day of the week 	Sunday through Saturday
			'N' => 'E',     // ISO-8601 numeric representation of the day of the week, 1 (for Monday) through 7 (for Sunday)
			'S' => 'Do',    // English ordinal suffix for the day of the month, 2 characters 	st, nd, rd or th. Works well with j
			'w' => 'd',     // Numeric representation of the day of the week 	0 (for Sunday) through 6 (for Saturday)
			'z' => '',     	// The day of the year (starting from 0) 	0 through 365
			// Week
			'W' => 'w',     // ISO-8601 week number of year, weeks starting on Monday (added in PHP 4.1.0) 	Example: 42 (the 42nd week in the year)
			// Month
			'F' => 'MMMM',  // A full textual representation of a month, January through December
			'm' => 'MM',    // Numeric representation of a month, with leading zeros 	01 through 12
			'M' => 'MMM',   // A short textual representation of a month, three letters 	Jan through Dec
			'n' => 'M	',    // Numeric representation of a month, without leading zeros 	1 through 12
			't' => '',      // Number of days in the given month 	28 through 31
			// Year
			'L' => '',      // Whether it's a leap year, 1 if it is a leap year, 0 otherwise.
			'o' => 'Y',     // ISO-8601 year number. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead.
			'Y' => 'YYYY',  // A full numeric representation of a year, 4 digits 	Examples: 1999 or 2003
			'y' => 'YY',    // A two digit representation of a year 	Examples: 99 or 03
			// Time
			'a' => 'a',     // Lowercase Ante meridiem and Post meridiem, am or pm
			'A' => 'A',     // Uppercase Ante meridiem and Post meridiem, AM or PM, not supported by ICU but we fallback to lowercase
			'B' => '',      // Swatch Internet time 	000 through 999
			'g' => 'h',     // 12-hour format of an hour without leading zeros 	1 through 12
			'G' => 'H',     // 24-hour format of an hour without leading zeros 0 to 23h
			'h' => 'hh',    // 12-hour format of an hour with leading zeros, 01 to 12 h
			'H' => 'HH',    // 24-hour format of an hour with leading zeros, 00 to 23 h
			'i' => 'mm',    // Minutes with leading zeros 	00 to 59
			's' => 'ss',    // Seconds, with leading zeros 	00 through 59
			'u' => '',      // Microseconds. Example: 654321
			// Timezone
			'e' => '',     	// Timezone identifier. Examples: UTC, GMT, Atlantic/Azores
			'I' => '',      // Whether or not the date is in daylight saving time, 1 if Daylight Saving Time, 0 otherwise.
			'O' => '',      // Difference to Greenwich time (GMT) in hours, Example: +0200
			'P' => '',      // Difference to Greenwich time (GMT) with colon between hours and minutes, Example: +02:00
			'T' => '',      // Timezone abbreviation, Examples: EST, MDT ...
			'Z' => '',      // Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. -43200 through 50400
			// Full Date/Time
			'c' => '', 			// ISO 8601 date, e.g. 2004-02-12T15:19:21+00:00, skipping the time here because it is not supported
			'r' => '', 			// RFC 2822 formatted date, Example: Thu, 21 Dec 2000 16:01:07 +0200, skipping the time here because it is not supported
			'U' => 'X',     // Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
		]);
	}
}
