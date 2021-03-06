<?php
	/* Author:	chive@github
			Kim Thoenen
			kim@smuzey.ch
				
			smuzey Web Design & Development
			www.smuzey.ch
	*/
	
	// Please set your API Key here
	// You can get it from here: https://www.pipelinedeals.com/admin/api (API_KEY)
	$api_key = '';

	// Base URL (shouldn't be changed)
	$base_url = "https://api.pipelinedeals.com/api/v3/";
	
	
	/* API Adapter Function / Shorthand version
	     Arguments
		   $res		The wanted resource name, e.g.: "deals" or "admin/lead_statuses"
		   $conditions	Conditions in key=>value array, e.g.: "array('[deal_created][from_date]'=>'2011-01-01','[deal_created][to_date]' => '2012-01-01')"
		   $page	The wanted page as integer, e.g.: "2"
		   $postdata	For POST requests, e.g.: "deal[name]=Kinda a big deal"
		 Return value
		   array	JSON decoded PHP array, ready to use in your application
	*/
		
	function getData ($res, $conditions = null, $page = null) {
		global $api_key; global $base_url;
		
		// Constructing URL
		$url = $base_url . $res . ".json?api_key=" . $api_key;
		
		// Adding conditions
		if (!empty($conditions)) {
			foreach ($conditions as $key=>$value) {
				$url .= "&conditions" . $key . "=" . $value;
			}
		}
		
		// Adding page
		if (!empty($page)) { $url .= "&page=" . $page; }
		
		return getJSON(curl($url));
	}
	
	function postData ($res, $postdata) {
		global $api_key; global $base_url;

		// Constructing URL
		$url = $base_url . $res . ".json?api_key=" . $api_key;

		return getJSON(curl($url,"post",$postdata));
	}

	function putData ($res, $putdata) {
		global $api_key; global $base_url;

		// Constructing URL
		$url = $base_url . $res . ".json?api_key=" . $api_key;

		return getJSON(curl($url,"put",$putdata));
	}
	
	/* API Adapter Function / URL version
	     Arguments
		   $url		Whole URL except the API_KEY part, e.g.: "https://api.pipelinedeals.com/api/v3/deals.json"
		   $postdata	For POST requests, e.g.: "deal[name]=Kinda a big deal"
		 Return value
		   array	JSON decoded PHP array, ready to use in your application
	*/
	
	function getDataURL ($url,$postdata = false) {
		global $api_key;
		
		// Do we already have arguments in the URL query string
		if (strstr($url, '?')) { $sign = '&'; }
		else { $sign = '?';	}
		
		// Construct string
		$url .= $sign . "api_key=" . $api_key;

		// POST data
		$poststring = "";
		if (!empty($postdata)) {
			foreach ($postdata as $field=>$value) {
				$poststring .= $field . "=" . $value . ";";
			}
		}
		
		return getJSON(curl($url,$postdata));
	}
	
	
	// ***************************** //
	// Do not modify anything below! //
	// ***************************** //
		
	// Returns data from cURL request
	function curl($url,$datatype = null, $data = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_COOKIESESSION,0); 
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . "/cookie.txt");
		curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . "/cookie.txt");
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		if (!empty($datatype)) {
			if ($datatype == "post") {
				curl_setopt($ch, CURLOPT_POST, 1); 
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
			}

			else if ($datatype == "put") {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
			}
		}

		
		$res = curl_exec($ch);
		curl_close($ch);
		
		return $res;
	}
	
	// Returns json_decoded resource
	function getJSON ($res) {
		return json_decode((string)$res,true);
	}
