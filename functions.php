<?php /*
--- Main Function Library
-- by R3dnix™
------------------------------------------------------------------------*/

# General Functions

	# Get current page from the url without extenstion..
	function currentPage() {
		return basename($_SERVER['PHP_SELF'], '.php');
	}
	
	# Get the base PATH of root, core, or current location..
	// echo base_url();    //  will produce something like: http://domain.com/questions/2820723/
	// echo base_url(TRUE);    //  will produce something like: http://domain.com/
	// echo base_url(TRUE, TRUE); || echo base_url(NULL, TRUE);    //  will produce something like: http://domain.com/questions/
	// echo base_url(NULL, NULL, TRUE);
	//      array(3) {
	//          ["scheme"]=>
	//          string(4) "http"
	//          ["host"]=>
	//          string(12) "domain.com"
	//          ["path"]=>
	//          string(35) "/questions/2820723/"
	//      }

    function base_url($atRoot=FALSE, $atCore=FALSE, $parse=FALSE){
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
            
            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), -1, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];
            
            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf( $tmplt, $http, $hostname, $end );
        }
        else $base_url = 'http://localhost/';
        
        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
        }
        
        return $base_url;
    }


	function uniq_id($lenght = 13) {
		if (function_exists("random_bytes")) {
			$bytes = random_bytes(ceil($lenght / 2));
		} elseif (function_exists("openssl_random_pseudo_bytes")) {
			$bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
		} else {
			throw new Exception("no cryptographically secure random function available");
		}
		return substr(bin2hex($bytes), 0, $lenght);
	}
	
# Financial Functions

	function btcToSatoshi($btc) {
		return ($btc)*(pow(10, 8)); 
	}
	
	function satoshiToBTC($satoshi, $length = null) {
		return $value = isset($length) ? rtrim(rtrim(sprintf('%.'.$length.'f', $satoshi / 100000000), '0'), '.') : rtrim(rtrim(sprintf('%.8f', $satoshi / 100000000), '0'), '.');
	}
	
	function btcToUSD($btc, $crypto_rates) {
		return number_format((float)$btc * $crypto_rates["btcusd"], 2, '.', '');;
	}
		
	function usdToBTC($usd) {
		$data = file_get_contents("https://apirone.com/api/v1/tobtc?currency=USD&value=" .$usd. "");
		return $data;
	}

	function convertToBTCFromSatoshi($value) {
		$BTC = $value / 100000000 ;
		return $BTC;
	}
	function formatBTC($value) {
		$value = sprintf('%.8f', $value);
		$value = rtrim($value, '0') . ' BTC';
		return $value;
	}

# Validation Functions

	function validate_email($email) {
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
		else return true;
	}
	
# Cookie Functions

	
	
# Debug Functions

	function printr($array) {
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
	
	function writeToFile($content, $file, $single_line = false) {
		if($single_line == true) $fp = fopen($file, "w");
		else {
			$content = $content. PHP_EOL;
			$fp = fopen($file, "a");
		}
		fwrite($fp, $content);
		fclose($fp);
	}
	
# Sanitization Functions

	function sanitize($str) {
		$str = str_replace(array("$", "%", "#", "", "|", "€", "", "{", "[", "]", "}", "}", "£", "'", "="), "", $str);
		$str = strip_tags($str);
		$str = stripslashes($str);
		if(strlen($str) > 501) {
			die("String exceeded maximum length.");
		}
		return $str;
	}
	
# Output Functions

	function get_time_ago($time) {
		$time_difference = time() - $time;
		if($time_difference < 60 * 5) { return "online"; }
		$condition = array(12 * 30 * 24 * 60 * 60 => "year",
					30 * 24 * 60 * 60 => "month",
					24 * 60 * 60 => "day",
					60 * 60 => "hour",
					60 => "minute",
					1 => "second"
		);
		foreach($condition as $secs => $str) {
			$d = $time_difference / $secs;
			if($d >= 1) {
				$t = round( $d );
				//return $t . " ". $str . ( $t > 1 ? "s" : "" ) . " ago";
				return $t . " ". $str . ( $t > 1 ? "s" : "" );
			}
		}
	}
	
	function get_time_ago_plain($time) {
		$time_difference = time() - $time;
		$condition = array(12 * 30 * 24 * 60 * 60 => "year",
					30 * 24 * 60 * 60 => "month",
					24 * 60 * 60 => "day",
					60 * 60 => "hour",
					60 => "minute",
					1 => "second"
		);
		foreach($condition as $secs => $str) {
			$d = $time_difference / $secs;
			if($d >= 1) {
				$t = round( $d );
				return $t . " ". $str . ( $t > 1 ? "s" : "" ) . " ago";
			}
		}
	}
	
	function get_time_ago_days($time) {
		$time_difference = time() - $time;
		$condition = array(
					24 * 60 * 60 => ""
		);
		foreach($condition as $secs => $str) {
			$d = $time_difference / $secs;
			if($d >= 1) {
				$t = round( $d );
				return $t;
			}
		}
	}
	
	function time_left($time_left = 0, $endtime = null) {
		if($endtime != null) 
			$time_left = $endtime - time(); 
		if($time_left > 0) { 
			$days = floor($time_left / 86400); 
			$time_left = $time_left - $days * 86400; 
			$hours = floor($time_left / 3600); 
			$time_left = $time_left - $hours * 3600; 
			$minutes = floor($time_left / 60); 
			$seconds = $time_left - $minutes * 60; 
		} else { 
			return array(0, 0, 0, 0); 
		} 
		return array($days, $hours, $minutes, $seconds); 
	}

	function shortenString($string, $length, $trailingChar = '') {
		if(is_string($string) === false) {
			return false;
		}
		if(mb_strlen($string) > $length) {
			return mb_substr($string, 0, ($length - mb_strlen($trailingChar))) . $trailingChar;
		} else {
			return $string;
		}
	}
	
	// Function to get country by IP Address using a 3rd party API..
	function ip_to_country($ip) {
		$content = file_get_contents("https://api.country.is/" .$ip);
		$result = json_decode($content, true);
		if(!$content) return "";
		else return $result["country"];
	}
?>
