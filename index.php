<?php

	/**
	 * Determine client's IP address and return it to them
	 *
	 * Clients need a password in order to get their IP returned to them.
	 * If they don't have one, they'll get a 403 instead.
	 *
	 * @author Gabriel Comeau
	 */

	// Put SHA1 hashes of the passwords you want to work in here
	$validHashes = array('');

	// Check if they've posted their password to us and we've got it
	if (isset($_POST['password']) && inPasswordList($_POST['password'], $validHashes)) {
		// Did a proxy request this for someone?  That'd kind of ruin things
		$clientIp = "";
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// X-FORWARDED-FOR gives a comma+space delimited list of IPs, client
			// first and then the succession of proxies after
			$xffParts = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			$clientIp = $xffParts[0];
		} else {
			$clientIp = $_SERVER['REMOTE_ADDR'];
		}

		if ($clientIp != "") {
			echo $clientIp;
		} else {
			// Uh we don't have an IP
			errorOut(500);
		}

	} else {
		// No password match
		errorOut(403);
	}

	/**
	 * Compare a password to an array of hashed passwords
	 */
	function inPasswordList($toCheck, $validHashes) {
		$hashedToCheck = sha1($toCheck);
		foreach ($validHashes as $hash) {
			if ($hashedToCheck == $hash) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Need this function because http_response_code() is too new!
	 */
	function errorOut($errorCode) {
		if (function_exists("http_response_code")) {
			http_response_code($errorCode);
		} else {
			switch($errorCode) {
				case 403:
					header("HTTP/1.0 403 Forbidden", 403);	
					break;
			  default:
					header("HTTP/1.0 Internal Server Error", 500);
			}
		}
	}
