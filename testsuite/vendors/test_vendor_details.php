<?php
/*
	test_vendor_details.php

	Copyright (c) 2009 Amberdms Ltd


	TESTSUITE SCRIPT

	Tests following SOAP APIs:
	* authenticate (login function)
	* vendors_manage (all functions)

	This script performs the following actions:
	1. Connects to the billing system
	2. Creates a new vendor and returns the ID
	3. Fetch the data for the vendor
	4. Deletes the vendor.


	----
	Permission is hereby granted, free of charge, to any person
	obtaining a copy of this software and associated documentation
	files (the "Software"), to deal in the Software without
	restriction, including without limitation the rights to use,
	copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the
	Software is furnished to do so, subject to the following
	conditions:

	The above copyright notice and this permission notice shall be
	included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
	OTHER DEALINGS IN THE SOFTWARE.
	----
*/


/*
	CONFIGURATION
*/

$url            = "https://devel-webapps.local.amberdms.com/development/amberdms_opensource/oss-amberdms-bs/trunk/api/";
//$url		= "https://www.amberdms.com/products/billing_system/online/api/";

$auth_account	= "devel";		// only used by Amberdms Billing System - Hosted Version
$auth_username	= "setup";
$auth_password	= "setup123";



/*
	1. AUTHENTICATE
*/

// connect
$client = new SoapClient("$url/authenticate/authenticate.wsdl");
$client->__setLocation("$url/authenticate/authenticate.php");


// login & get PHP session ID
try
{
	$sessionid = $client->login($auth_account, $auth_username, $auth_password);
}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");
}

unset($client);


/*
	2. GATHER DATA

	This section is a good place to add your own code to fetch the data you need to post to the system.
*/

$data["name_vendor"]		= "SOAP API Testscript";
$data["code_vendor"]		= "TEST_VENDOR";
$data["date_start"]		= date("Y-m-d");
$data["name_contact"]		= "Accounts Manager";
$data["contact_email"]		= "test@example.com";
$data["contact_phone"]		= "12 123 1234";
$data["discount"]		= "15";

$data_tax["taxid"]		= 1;
$data_tax["status"]		= "on";




/*
	3. CONNECT TO VENDORS_MANAGE SERVICE

*/

$client = new SoapClient("$url/vendors/vendors_manage.wsdl");
$client->__setLocation("$url/vendors/vendors_manage.php?$sessionid");




/*
	4. CREATE NEW VENDOR
*/

try
{
	print "Creating new vendor...\n";

	// upload data and get ID back
	$data["id"] = $client->set_vendor_details($data["id"],
							$data["code_vendor"],
							$data["name_vendor"],
							$data["name_contact"],
							$data["contact_email"],
							$data["contact_phone"],
							$data["contact_fax"],
							$data["date_start"],
							$data["date_end"],
							$data["tax_number"],
							$data["tax_default"],
							$data["address1_street"],
							$data["address1_city"],
							$data["address1_state"],
							$data["address1_country"],
							$data["address1_zipcode"],
							$data["address2_street"],
							$data["address2_city"],
							$data["address2_state"],
							$data["address2_country"],
							$data["address2_zipcode"],
							$data["discount"]);

	print "Created new vendor with ID of ". $data["id"] ."\n";


	// enable a tax
	print "Enabling tax...\n";
	
	$client->set_vendor_tax($data["id"], $data_tax["taxid"], $data_tax["status"]);



}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");

}


/*
	5. SELECT VENDOR DETAILS
*/


try
{
	print "Executing get_vendor_details for ID ". $data["id"] ."\n";
	$data_tmp = $client->get_vendor_details($data["id"]);
	print_r($data_tmp);

	print "Executing get_vendor_tax for ID ". $data["id"] ."\n";
	$data_tmp = $client->get_vendor_tax($data["id"]);
	print_r($data_tmp);


}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");
}




/*
	6. DELETE VENDOR
*/



try
{
	print "Deleting vendor with ID of ". $data["id"] ."\n";
	$client->delete_vendor($data["id"]);

}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");
}



?>
