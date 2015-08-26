<?php

use Namshi\JOSE\SimpleJWS;

class LayerIdentityTokenProviderTest extends PHPUnit_Framework_TestCase
{
    public function testGenerateIdentityToken()
    {
        $layerIdentityTokenProvider = new \Layer\LayerIdentityTokenProvider();
        $privateKey                 = <<<EOF
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDhfR1nIkmSyGUmpTajKgUWpm2f3ObmCgZMTNvadYzwBoJ3ktRG
t3cSvRdsyo1EZQPKLOOLv4sXxTE4RrMu/xBnbvUYzn9uvygcW8YT+gW1taJ7GM01
qjKfhAGbC9fhVvJC6VZRmk/ioGBYkA3+7lZTil33szwBK/REcWlPpXOH5wIDAQAB
AoGAECEzCT2apbVQBwOqdOF8m7IsBVN38Nymtq6Iy4e9HS5aBtOp+6UED4MXOeED
WfEf5EZxwH1jJcAlVTE5gBMeyST0dZ1BYrHU/RKtPAb/RqoxIy2ON9lQOzV+xR/Z
0W8LcrAHbIgu7iBGecTSsTrNw0i5Wo4684gEMM3MDtkbIQECQQD2W00r9CA+A8uL
xXa/p/8YLw3He4tAeU13qb7W/Wx0RfF5oZT3aqUwvgLTDP+ASycFUAD1MjKYOQpP
mwDu70eZAkEA6lCzE77b3xWFsNv9GysqTYQr3CoNmxWwGOdxsBsKrmuRdwRu5YvG
p00JG48VaNs5RXTiO42kefjHkPCQ1Wz7fwJBAOJNWISpyvxsrAwHJmBESHbEspmu
iWp+g4UK7v266mec4IdkwNzOoFQ4F4wcApCteHjO1zJmHEftDeW2c5MJRvECQHOO
wxJs4UC++4UCqWv5uM4r7fmRn84pPwS5N/9TBsyIbmAVBqAcdCdUPbaitTtWSoNv
ppcaPtCMmddoXPV03v8CQE01dePAfsVIACSSHTFSx9nmLzRmMqFT04uaBKDcqgEw
Ks3Omb1JuXYxR4elMX4d5Y3JPUMbqUPKylnE4X9ogbc=
-----END RSA PRIVATE KEY-----
EOF;
        $layerIdentityTokenProvider->setPrivateKey($privateKey);
        $layerIdentityTokenProvider->setKeyID('foo');
        $layerIdentityTokenProvider->setProviderID('bar');

        $identityToken = $layerIdentityTokenProvider->generateIdentityToken('sean', 'nonce');

        $publicKey         = <<<EOF
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDhfR1nIkmSyGUmpTajKgUWpm2f
3ObmCgZMTNvadYzwBoJ3ktRGt3cSvRdsyo1EZQPKLOOLv4sXxTE4RrMu/xBnbvUY
zn9uvygcW8YT+gW1taJ7GM01qjKfhAGbC9fhVvJC6VZRmk/ioGBYkA3+7lZTil33
szwBK/REcWlPpXOH5wIDAQAB
-----END PUBLIC KEY-----
EOF;
        $expectedISSResult = 'bar';
        $expectedPRNResult = 'sean';

        $public_key = openssl_pkey_get_public($publicKey);
        $jws        = SimpleJWS::load($identityToken);
        if ($jws->isValid($public_key, 'RS256')) {
            $payload = $jws->getPayload();
            $this->assertEquals($expectedISSResult, $payload['iss'], 'iss did not match expected value');
            $this->assertEquals($expectedPRNResult, $payload['prn'], 'prn did not match expected value');
        } else {
            $this->assertFalse($jws->isValid($public_key, 'RS256'), 'SimpleJWS did not create a valid identity token');
        }
    }
}
