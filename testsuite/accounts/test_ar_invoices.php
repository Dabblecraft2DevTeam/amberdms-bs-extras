<?php
/*
	test_ar_invoices.php

	TESTSUITE SCRIPT

	Tests following SOAP APIs:
	* authenticate (login function)
	* accounts_invoices_manage (all functions)

	This script performs the following actions:
	* Connects to the billing system
	* Creates a new invoice and returns the ID
	* Fetch the data for the invoice
	* Deletes the invoice
*/


/*
	CONFIGURATION
*/

$url		= "https://devel-centos5-64.jethrocarr.local/development/amberdms/billing_system/htdocs/api";

$auth_account	= 0;		// only used by Amberdms Billing System - Hosted Version
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

// set the below ID to update an invoice, rather than create a new one
//$data["id"]			= "57";

// invoice details
$data["invoicetype"]		= "ar";
$data["locked"]			= 0;
$data["orgid"]			= "2";
$data["employeeid"]		= "4";
$data["dest_account"]		= "2";
$data["code_invoice"]		= "";
$data["code_ordernumber"]	= "";
$data["code_ponumber"]		= "";
$data["date_due"]		= date("Y-m-d");
$data["date_trans"]		= date("Y-m-d");
$data["date_sent"]		= date("Y-m-d");
$data["sentmethod"]		= "";
$data["notes"]			= "SOAP API TEST INVOICE";
$data["autotaxes"]		= "off";


// define standard item
$data["item"]["standard"]["id"]			= "";		// set to update existing items
$data["item"]["standard"]["chartid"]		= 1;
$data["item"]["standard"]["amount"]		= "100.00";
$data["item"]["standard"]["description"]	= "SOAP test standard item";

// define product item
$data["item"]["product"]["id"]			= "";		// set to update existing items
$data["item"]["product"]["price"]		= "10.00";
$data["item"]["product"]["quantity"]		= "5";
$data["item"]["product"]["units"]		= "Items";
$data["item"]["product"]["productid"]		= "1";
$data["item"]["product"]["description"]		= "SOAP test product item";

// define time item
$data["item"]["time"]["id"]			= "";			// set to update existing items
$data["item"]["time"]["price"]			= "10.00";
$data["item"]["time"]["productid"]		= "1";
$data["item"]["time"]["timegroupid"]		= "1";
$data["item"]["time"]["description"]		= "SOAP test time item";

// define tax item
$data["item"]["tax"]["id"]			= "";			// set to update existing items
$data["item"]["tax"]["taxid"]			= "2";
$data["item"]["tax"]["manual_option"]		= "";
$data["item"]["tax"]["manual_amount"]		= "";

// define payment item
$data["item"]["payment"]["id"]			= "";			// set to update existing items
$data["item"]["payment"]["date_trans"]		= date("Y-m-d");
$data["item"]["payment"]["chartid"]		= "4";
$data["item"]["payment"]["amount"]		= "65.00";
$data["item"]["payment"]["source"]		= "cheque";
$data["item"]["payment"]["description"]		= "SOAP test customer payment";





/*
	CONNECT TO ACCOUNTS_CHARTS_MANAGE SERVICE

*/

$client = new SoapClient("$url/accounts/invoices_manage.wsdl");
$client->__setLocation("$url/accounts/invoices_manage.php?$sessionid");




/*
	CREATE NEW INVOICE
*/





// create account
try
{
	print "Creating new invoice...\n";

	// upload data and get ID back
	$data["id"] = $client->set_invoice_details($data["id"],
							$data["invoicetype"],
							$data["locked"],
							$data["orgid"],
							$data["employeeid"],
							$data["dest_account"],
							$data["code_invoice"],
							$data["code_ordernumber"],
							$data["code_ponumber"],
							$data["date_due"],
							$data["date_trans"],
							$data["date_sent"],
							$data["sentmethod"],
							$data["notes"],
							$data["autotaxes"]);

	print "Created new invoice with ID of ". $data["id"] ."\n";
}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");

}




// create items, taxes and payments
try
{
	print "Creating standard item...\n";

	// upload data and get ID back
	$data["item"]["standard"]["id"] = $client->set_invoice_item_standard($data["id"],
										$data["invoicetype"],
										$data["item"]["standard"]["id"],
										$data["item"]["standard"]["chartid"],
										$data["item"]["standard"]["amount"],
										$data["item"]["standard"]["description"]);


	print "Creating product item...\n";

	// upload data and get ID back
	$result = $client->set_invoice_item_product($data["id"],
							$data["invoicetype"],
							$data["item"]["product"]["id"],
							$data["item"]["product"]["price"],
							$data["item"]["product"]["quantity"],
							$data["item"]["product"]["units"],
							$data["item"]["product"]["productid"],
							$data["item"]["product"]["description"]);

	print "Creating time item...\n";

	// upload data and get ID back
	$result = $client->set_invoice_item_time($data["id"],
							$data["invoicetype"],
							$data["item"]["time"]["id"],
							$data["item"]["time"]["price"],
							$data["item"]["time"]["productid"],
							$data["item"]["time"]["timegroupid"],
							$data["item"]["time"]["description"]);

	print "Creating tax item...\n";

	// upload data and get ID back
	$result = $client->set_invoice_tax($data["id"],
							$data["invoicetype"],
							$data["item"]["tax"]["id"],
							$data["item"]["tax"]["taxid"],
							$data["item"]["tax"]["manual_option"],
							$data["item"]["tax"]["manual_amount"]);

	print "Creating payment item...\n";

	// upload data and get ID back
	$result = $client->set_invoice_payment($data["id"],
							$data["invoicetype"],
							$data["item"]["payment"]["id"],
							$data["item"]["payment"]["date_trans"],
							$data["item"]["payment"]["chartid"],
							$data["item"]["payment"]["amount"],
							$data["item"]["payment"]["source"],
							$data["item"]["payment"]["description"]);

}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");

}






/*
	GET INVOICE DETAILS
*/

try
{
	$data_tmp = $client->get_invoice_details($data["id"], $data["invoicetype"]);

	print "Executing get_invoice_details for ID ". $data["id"] ."\n";
	print_r($data_tmp);

}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");
}






/*
	GET INVOICE ITEMS
*/
try
{
	// items
	$data_tmp = $client->get_invoice_items($data["id"], $data["invoicetype"]);

	print "Executing get_invoice_items for ID ". $data["id"] ."\n";
	print_r($data_tmp);


	// taxes
	$data_tmp = $client->get_invoice_taxes($data["id"], $data["invoicetype"]);

	print "Executing get_invoice_taxes for ID ". $data["id"] ."\n";
	print_r($data_tmp);


	// payments
	$data_tmp = $client->get_invoice_payments($data["id"], $data["invoicetype"]);

	print "Executing get_invoice_payments for ID ". $data["id"] ."\n";
	print_r($data_tmp);


}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");
}




/*
	DELETE INVOICE
*/


// delete invoice standard item
try
{
	print "Deleting invoice standard item with ID of ". $data["item"]["standard"]["id"] ."\n";
	$client->delete_invoice_item($data["item"]["standard"]["id"]);


	print "Deleting invoice with ID of ". $data["id"] ."\n";
	$client->delete_invoice($data["id"], $data["invoicetype"]);
}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");
}



?>