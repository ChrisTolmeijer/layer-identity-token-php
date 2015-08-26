<?php


namespace Layer;

use Namshi\JOSE\SimpleJWS;

// SimpleJWS requires timezeone to be set
if (!ini_get('date.timezone')) {
    date_default_timezone_set('GMT');
}

/**
 * Layer Identity Token Provider.
 *
 * PHP version 5
 *
 * @category Authentication
 *
 * @author   Abir Majumdar <abir@layer.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0
 *
 * @link     https://github.com/layerhq/support/tree/master/identity-services-samples/php
 */
class LayerIdentityTokenProvider implements LayerIdentityTokenProviderInterface
{
    private $_providerID;
    private $_keyID;
    private $_privateKey;

    /**
     * Constructor.
     *
     * @param string $providerID
     * @param string $keyID
     * @param string $privateKey
     */
    public function __construct()
    {
        $providerID = getenv('LAYER_PROVIDER_ID');
        $keyID      = getenv('LAYER_PRIVATE_KEY_ID');
        $privateKey = getenv('LAYER_PRIVATE_KEY');

        $this->setProviderID($providerID);
        $this->setKeyID($keyID);
        $this->setPrivateKey($privateKey);
    }

    /**
     * Sets Provider ID.
     *
     * @param string $providerID Token
     */
    public function setProviderID($providerID)
    {
        $this->_providerID = $providerID;
    }

    /**
     * Sets Key ID.
     *
     * @param string $keyID Token
     */
    public function setKeyID($keyID)
    {
        $this->_keyID = $keyID;
    }

    /**
     * Sets Private Key.
     *
     * @param string $privateKey Token
     */
    public function setPrivateKey($privateKey)
    {
        $this->_privateKey = $privateKey;
    }

    /**
     * Checks if all the proper config has been prvided.
     */
    private function _checkLayerConfig()
    {
        $errorString = array();
        if ($this->_providerID == '') {
            array_push($errorString, 'LAYER_PROVIDER_ID');
        }

        if ($this->_keyID == '') {
            array_push($errorString, 'LAYER_PRIVATE_KEY_ID');
        }

        if ($this->_privateKey == '') {
            array_push($errorString, 'LAYER_PRIVATE_KEY');
        }

        if (count($errorString) > 0) {
            $joined = implode(',', $errorString);
            trigger_error("$joined  not configured. See README.md", E_USER_ERROR);
        }
    }

    public function generateIdentityToken($user_id, $nonce)
    {
        $this->_checkLayerConfig();

        $jws = new SimpleJWS(array(
            // String - Expresses a MIME Type of application/JWT
            'typ' => 'JWT',
            // String - Expresses the type of algorithm used to sign the token;
            // must be RS256
            'alg' => 'RS256',
            // String - Express a Content Type of Layer External Identity Token,
            // version 1
            'cty' => 'layer-eit;v=1',
            // String - Private Key associated with "layer.pem", found in the
            // Layer Dashboard
            'kid' => $this->_keyID
        ));
        $jws->setPayload(array(
            // String - The Provider ID found in the Layer Dashboard
            'iss' => $this->_providerID,
            // String - Provider's internal ID for the authenticating user
            'prn' => $user_id,
            // Integer - Time of Token Issuance in RFC 3339 seconds
            'iat' => round(microtime(true) * 1000),
            // Integer - Token Expiration in RFC 3339 seconds; set to 2 minutes
            'exp' => round(microtime(true) * 1000) + 120,
            # The nonce obtained via the Layer client SDK.
            'nce' => $nonce
        ));

        $privateKey = openssl_pkey_get_private($this->_privateKey);
        $jws->sign($privateKey);
        $identityToken = $jws->getTokenString();

        return $identityToken;
    }
}
