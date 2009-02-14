<?php
/*
	test_products.php

	TESTSUITE SCRIPT

	Tests following SOAP APIs:
	* authenticate (login function)
	* accounts_products_manage (all functions)

	This script performs the following actions:
	* Connects to the billing system
	* Creates a new product and returns the ID
	* Fetch the data for the product
	* Deletes the product
*/


/*
	CONFIGURATION
*/

$url		= "https://devel-centos5-64.jethrocarr.local/development/amberdms/billing_system/htdocs/api";

$auth_account	= "amberdms";		// only used by Amberdms Billing System - Hosted Version
$auth_username	= "soap";
$auth_password	= "setup123";



/*
	AUTHENTICATE
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
	GATHER DATA

	This section is a good place to add your own code to fetch the data you need to post to the system.
*/


$data["code_product"]		= "soap_test";
$data["name_product"]		= "soap test items";
$data["details"]		= "This item is created by the SOAP API testsuite";
$data["units"]			= "items";
$data["price_cost"]		= "15.00";
$data["price_sale"]		= "30.00";
$data["date_start"]		= date("Y-m-d");
$data["date_end"]		= "";
$data["date_current"]		= date("Y-m-d");
$data["quantity_instock"]	= "10";
$data["quantity_vendor"]	= "2153";
$data["vendorid"]		= "5";
$data["code_product_vendor"]	= "vendor code 24125";
$data["account_sales"]		= "9";
$data["account_purchase"]	= "10";


$data_tax["itemid"]		= 0;
$data_tax["taxid"]		= 1;
$data_tax["manual_option"]	= 0;
$data_tax["manual_amount"]	= 0;
$data_tax["description"]	= "SOAP TEST";



/*
	CONNECT TO PRODUCTS_MANAGE SERVICE

*/

$client = new SoapClient("$url/products/products_manage.wsdl");
$client->__setLocation("$url/products/products_manage.php?$sessionid");




/*
	CREATE NEW PRODUCT
*/



try
{
	print "Creating new product...\n";

	// upload data and get ID back
	$data["id"] = $client->set_product_details($data["id"],
							$data["code_product"],
							$data["name_product"],
							$data["units"],
							$data["details"],
							$data["price_cost"],
							$data["price_sale"],
							$data["date_start"],
							$data["date_end"],
							$data["date_current"],
							$data["quantity_instock"],
							$data["quantity_vendor"],
							$data["vendorid"],
							$data["code_product_vendor"],
							$data["account_sales"],
							$data["account_purchase"]);

	print "Created new product with ID of ". $data["id"] ."\n";


	// adding new tax
	$data_tax["id"] = $client->set_product_tax($data["id"], $data_tax["id"], $data_tax["taxid"], $data_tax["manual_option"], $data_tax["manual_amount"], $data_tax["description"]);

}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");

}






/*
	GET PRODUCT DETAILS
*/


try
{
	print "Executing get_product_details for ID ". $data["id"] ."\n";
	$data_tmp = $client->get_product_details($data["id"]);
	print_r($data_tmp);


	print "Executing get_product_taxes for ID ". $data["id"] ."\n";
	$data_tmp = $client->get_product_taxes($data["id"]);
	print_r($data_tmp);

}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");
}





/*
	DELETE PRODUCT
*/



try
{
	print "Delete tax item with ID of ". $data_tax["id"] ."\n";
	$client->delete_product_tax($data["id"], $data_tax["id"]);

	print "Listing remaining tax items:\n";
	$data_tmp = $client->get_product_taxes($data["id"]);
	print_r($data_tmp);

	print "Deleting product with ID of ". $data["id"] ."\n";
	$client->delete_product($data["id"]);

}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");
}




?>
