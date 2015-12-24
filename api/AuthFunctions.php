<?php
/*
	Created by Marlon Colhado
	admin@kazeta.com.br
*/

class SteamAuth {
	function intToByte($int)
	{
		return $int & (0xff);
	} 
	
	function startArrayToZero($array)
	{
		$mode = array();
		$intModeArray = 0;
		foreach($array as $test)
		{
			$mode[$intModeArray] = $this->intToByte($test);
			$intModeArray++;
		}
		return $mode;
	}
	
	function getSteamTime($localtime = false)
	{
		if($localtime) return time()+10;
		$data = array('steamid' => 0);
		$url = 'http://api.steampowered.com/ITwoFactorService/QueryTime/v0001';
		$ch = curl_init($url);
		$postString = http_build_query($data, '', '&');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$response = json_decode($response);
		curl_close($ch);

		return $response->response->server_time;
	}
	
	function createTimeHash($time)
	{
		$time /= 30;
		$timeArray = array();
		for($i = 8; $i > 0; $i--)
		{
			$timeArray[$i - 1] = $this->intToByte($time);
			$time >>= 8;
		}
		$timeArray = array_reverse($timeArray);
		$newTimeArray = "";
		foreach($timeArray as $timeArrayValue)
		{
			$newTimeArray .= chr($timeArrayValue);
		}
		return $newTimeArray;
	}
	
	function createHMac($timeHash, $SharedSecretDecoded)
	{
		$hash = hash_hmac('sha1', $timeHash, $SharedSecretDecoded, false);
		$hmac = unpack('C*', pack('H*', $hash));
		return $hmac;
	}
	
	function GenerateSteamGuardCode($shared_secret)
	{
		if($shared_secret == "Shared Secret Key") return "You need to change the 'Shared Secret Key' to your Shared Secret!";
		$DecodedSharedSecret = base64_decode($shared_secret);
		$timeHash = $this->createTimeHash($this->getSteamTime(true)); // If you need Steam Time instead the local time, use 'false'. (Using local time the response time is less)
		$HMAC = $this->createHMac($timeHash, $DecodedSharedSecret);
		$HMAC = $this->startArrayToZero($HMAC);
		
		$b = $this->intToByte(($HMAC[19] & 0xF));
		$codePoint = ($HMAC[$b] & 0x7F) << 24 | ($HMAC[$b+1] & 0xFF) << 16 | ($HMAC[$b+2] & 0xFF) << 8 | ($HMAC[$b+3] & 0xFF);
		
		$SteamChars = "23456789BCDFGHJKMNPQRTVWXY";
		$code = "";

		for($i = 0; $i < 5; $i++)
		{
			$code = $code."".$SteamChars{floor($codePoint) % strlen($SteamChars)};
			$codePoint /= strlen($SteamChars);
		}
		return $code;
	}
}

?>
