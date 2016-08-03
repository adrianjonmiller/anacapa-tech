<?php

require 'vendor/autoload.php';

if(!empty($_POST)) {

  try
  {
    // Instantiate a client.
    $client = new \BaseCRM\Client(['accessToken' => '6b582709aace0dc95c3758f0bab3d02275587c00d707e60eb41133ecc22d89ee']);
    $lead = $client->leads->create([
      'first_name'			=> $_POST['first_name'],
      'last_name' 			=> $_POST['last_name'],
      'company_name'		=> $_POST['company_name'],
      'email' 					=> $_POST['email'],
      'phone_number' 		=> $_POST['phone_number'],
      'address'         => [
        'line1'         => $_POST['address'],
        'city' 				  => $_POST['city'],
        'state' 				=> $_POST['state'],
        'postal_code'   => $_POST['zip_postal_code'],
        'country' 			=> $_POST['country'],
      ],
      'custom_fields'   => [
        "hh or ah"      => 1,
        'Product Type' 	=> $_POST['product_type'],
      ]
    ]);

    print_r($lead);
  }
  catch (\BaseCRM\Errors\ConfigurationError $e)
  {
    // Invalid client configuration option
  }
  catch (\BaseCRM\Errors\ResourceError $e)
  {
    // Resource related error
    print('Http status = ' . $e->getHttpStatusCode() . "\n");
    print('Request ID = ' . $e->getRequestId() . "\n");
    foreach ($e->errors as $error)
    {
      print('field = ' . $error['field'] . "\n");
      print('code = ' . $error['code'] . "\n");
      print('message = ' . $error['message'] . "\n");
      print('details = ' . $error['details'] . "\n");
    }
  }
  catch (\BaseCRM\Errors\RequestError $e)
  {
    // Invalid query parameters, authentication error etc.
  }
  catch (\BaseCRM\Errors\Connectionerror $e)
  {
    // Network communication error, curl error is returned
    print('Errno = ' . $e->getErrno() . "\n");
    print('Error message = ' . $e->getErrorMessage() . "\n");
  }
  catch (Exception $e)
  {
    // Other kind of exception
  }
} else {
  echo "Post is empty";
}

?>
