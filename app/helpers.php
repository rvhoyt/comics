<?php

function uploadToB2($file, $file_name, $bucket_id) {
  /*start authentication*/
  $application_key_id = "003e87c9b90e18e0000000002";
  $application_key = "K003toGUyRLs0djpLxUfzjN4vu614Tk";
  $credentials = base64_encode($application_key_id . ":" . $application_key);
  $url = "https://api.backblazeb2.com/b2api/v2/b2_authorize_account";

  $session = curl_init($url);

  // Add headers
  $headers = array();
  $headers[] = "Accept: application/json";
  $headers[] = "Authorization: Basic " . $credentials;
  curl_setopt($session, CURLOPT_HTTPHEADER, $headers);  // Add headers
  curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($session, CURLOPT_HTTPGET, true);  // HTTP GET
  curl_setopt($session, CURLOPT_RETURNTRANSFER, true); // Receive server response
  $server_output = curl_exec($session);
  curl_close ($session);
  $server_output = json_decode($server_output);

  
  
  /*get upload authorization*/
  $api_url = $server_output->apiUrl; // From b2_authorize_account call
  $auth_token = $server_output->authorizationToken; // From b2_authorize_account call

  $session = curl_init($api_url .  "/b2api/v2/b2_get_upload_url");

  // Add post fields
  $data = array("bucketId" => $bucket_id);
  $post_fields = json_encode($data);
  curl_setopt($session, CURLOPT_POSTFIELDS, $post_fields); 

  // Add headers
  $headers = array();
  $headers[] = "Authorization: " . $auth_token;
  curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);

  curl_setopt($session, CURLOPT_POST, true); // HTTP POST
  curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response
  $uploadRequest = curl_exec($session); // Let's do this!
  curl_close ($session); // Clean up
  $uploadRequest = json_decode($uploadRequest);
  
  
  
  /*start file upload*/
  $upload_url = $uploadRequest->uploadUrl; // Provided by b2_get_upload_url
  $upload_auth_token = $uploadRequest->authorizationToken; // Provided by b2_get_upload_url
  $bucket_id = $uploadRequest->bucketId;  // The ID of the bucket
  $content_type = "image/png";
  $sha1_of_file_data = sha1($file);

  $session = curl_init($upload_url);

  // Add read file as post field
  curl_setopt($session, CURLOPT_POSTFIELDS, $file); 

  // Add headers
  $headers = array();
  $headers[] = "Authorization: " . $upload_auth_token;
  $headers[] = "X-Bz-File-Name: " . $file_name;
  $headers[] = "Content-Type: " . $content_type;
  $headers[] = "X-Bz-Content-Sha1: " . $sha1_of_file_data;
  curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);

  curl_setopt($session, CURLOPT_POST, true); // HTTP POST
  curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response
  $upload = curl_exec($session); // Let's do this!
  curl_close ($session); // Clean up
  $upload = json_decode($upload);
  
  return $upload->fileId;
}



function deleteFromB2($file_id, $file_name) {
  /*start authentication*/
  $application_key_id = "003e87c9b90e18e0000000001";
  $application_key = "K003qGH/D84kwYTcN4KAEELVB/W6YH4";
  $credentials = base64_encode($application_key_id . ":" . $application_key);
  $url = "https://api.backblazeb2.com/b2api/v2/b2_authorize_account";

  $session = curl_init($url);

  // Add headers
  $headers = array();
  $headers[] = "Accept: application/json";
  $headers[] = "Authorization: Basic " . $credentials;
  curl_setopt($session, CURLOPT_HTTPHEADER, $headers);  // Add headers
  curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($session, CURLOPT_HTTPGET, true);  // HTTP GET
  curl_setopt($session, CURLOPT_RETURNTRANSFER, true); // Receive server response
  $server_output = curl_exec($session);
  curl_close ($session);
  $server_output = json_decode($server_output);
  
  
  $api_url = $server_output->apiUrl; // From b2_authorize_account call
  $auth_token = $server_output->authorizationToken; // From b2_authorize_account call

  $session = curl_init($api_url .  "/b2api/v2/b2_delete_file_version");

  // Add post fields
  $data = array("fileId" => $file_id, "fileName" => $file_name);
  $post_fields = json_encode($data);
  curl_setopt($session, CURLOPT_POSTFIELDS, $post_fields); 

  // Add headers
  $headers = array();
  $headers[] = "Authorization: " . $auth_token;
  curl_setopt($session, CURLOPT_HTTPHEADER, $headers); 

  curl_setopt($session, CURLOPT_POST, true); // HTTP POST
  curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response
  $server_output = curl_exec($session); // Let's do this!
  curl_close ($session); // Clean up
}