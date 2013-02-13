<?php

	// Put SHA1 hashes of the passwords you want to work in here
	$validHashes = array('');

	// Check if they've posted their password to us and we've got it
	if (isset($_POST['password']) && inPasswordList($_POST['password'], $validHashes)) {
		// Did a proxy request this for someone?  That'd kind of ruin things
		$clientIp = "";
		if (isset($_SERVER['X-FORWARDED-FOR'])) {
			// X-FORWARDED-FOR gives a comma+space delimited list of IPs, client
			// first and then the succession of proxies after
			$xffParts = explode(", ", $_SERVER['X-FORWARDED-FOR']);
			$clientIp = $xffParts[0];
		} else {
			$clientIp = $_SERVER['REMOTE_ADDR'];
		}

		if ($clientIp != "") {
			echo $clientIp;
		} else {
			// Uh we don't have an IP
			http_response_code(500);
		}

	} else {
		// No password match
		http_response_code(403);
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


