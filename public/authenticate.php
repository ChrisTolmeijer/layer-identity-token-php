<?php

require_once __DIR__.'/../vendor/autoload.php';

$layerIdentityTokenProvider = new \Layer\LayerIdentityTokenProvider();
// By default, LayerIdentityTokenProvider will look at the LAYER_PROVIDER_ID, LAYER_PRIVATE_KEY_ID, and LAYER_PRIVATE_KEY environment variables for credentials.
// You can overwrite those values by uncommenting the following lines and replace their values
//$layerIdentityTokenProvider->setProviderID("layer:///providers/...");
//$layerIdentityTokenProvider->setKeyID("layer:///keys/...");
//$layerIdentityTokenProvider->setPrivateKey("----BEGIN RSA PRIVATE KEY....");
$userID = $_POST["user_id"];
$nonce = $_POST["nonce"];

$identityToken = $layerIdentityTokenProvider->generateIdentityToken($userID, $nonce);
print '{"identity_token":"'.$identityToken.'"}';
