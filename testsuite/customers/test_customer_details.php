<?php
/*
	test_customer_details.php

	TESTSUITE SCRIPT

	Tests following SOAP APIs:
	* authenticate (login function)
	* customers_manage (all functions)

	This script performs the following actions:
	1. Connects to the billing system
	2. Creates a new customer and returns the ID
	3. Fetch the data for the customer
	4. Deletes the customer.
*/


/*
	CONFIGURATION
*/

$url		= "https://devel-centos5-64.jethrocarr.local/development/amberdms/billing_system/htdocs/api";

$auth_account	= 0;		// only used by Amberdms Billing System - Hosted Version
$auth_username	= "soap";
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

$data["name_customer"]		= "SOAP API Testscript";
$data["code_customer"]		= "TEST_CUSTOMER";
$data["date_start"]		= date("Y-m-d");
$data["contact_email"]		= "test@example.com";



/*
	3. CONNECT TO CUSTOMERS_MANAGE SERVICE

*/

$client = new SoapClient("$url/customers/customers_manage.wsdl");
$client->__setLocation("$url/customers/customers_manage.php?$sessionid");




/*
	4. CREATE NEW CUSTOMER
*/

try
{
	print "Creating new customer...\n";

	// upload data and get ID back
	$data["id"] = $client->set_customer_details($data["id"],
							$data["code_customer"],
							$data["name_customer"],
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
							$data["address2_zipcode"]);

	print "Created new customer with ID of ". $data["id"] ."\n";
}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");

}


/*
	5. SELECT CUSTOMER DETAILS
*/


try
{
	$data_tmp = $client->get_customer_details($data["id"]);

	print "Executing get_customer_details for ID ". $data["id"] ."\n";
	print_r($data_tmp);

}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");
}




/*
	6. DELETE CUSTOMER
*/



try
{
	print "Deleting customer with ID of ". $data["id"] ."\n";
	$client->delete_customer($data["id"]);

}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");
}



?>