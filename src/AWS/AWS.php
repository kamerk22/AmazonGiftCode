<?php

/**
 * Part of the AmazonGiftCode package.
 * Author: Kashyap Merai <kashyapk62@gmail.com>
 *
 */


namespace kamerk22\AmazonGiftCode\AWS;


use kamerk22\AmazonGiftCode\Client\Client;

class AWS
{
    const SERVICE_NAME = "AGCODService";
    const ACCEPT_HEADER = "accept";
    const CONTENT_HEADER = "content-type";
    const HOST_HEADER = "host";
    const X_AMZ_DATE_HEADER = "x-amz-date";
    const X_AMZ_TARGET_HEADER = "x-amz-target";
    const AUTHORIZATION_HEADER = "Authorization";
    const AWS_SHA256_ALGORITHM = "AWS4-HMAC-SHA256";
    const KEY_QUALIFIER = "AWS4";
    const TERMINATION_STRING = "aws4_request";

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getCode($amount)
    {
        $serviceOperation = 'CreateGiftCard';
        $payload = $this->getGiftCardPayload($amount);
        $canonicalRequest = $this->getCanonicalRequest($serviceOperation, $payload);
        $dateTimeString = $this->getTimeStamp();
        return $this->makeRequest($payload, $canonicalRequest, $serviceOperation, $dateTimeString);

    }

    public function makeRequest($payload, $canonicalRequest, $serviceOperation, $dateTimeString)
    {
        $KEY_QUALIFIER = self::KEY_QUALIFIER;
        $canonicalRequestHash = $this->buildHash($canonicalRequest);
        $stringToSign = $this->buildStringToSign($canonicalRequestHash);
        $authorizationValue = $this->buildAuthSignature($stringToSign);

        $secretKey = $this->config->getSecret();
        $endpoint = $this->config->getEndpoint();
        $regionName = $this->getRegion();

        $SERVICE_NAME = "AGCODService";
        $serviceTarget = "com.amazonaws.agcod." . $SERVICE_NAME . "." . $serviceOperation;
        $dateString = $this->getDateString();

        $signatureAWSKey = $KEY_QUALIFIER . $secretKey;

        $kDate = $this->hmac($dateString, $signatureAWSKey);
        $kDate_hexis = $this->hmac($dateString, $signatureAWSKey, false);
        $kRegion = $this->hmac($regionName, $kDate);
        $kRegion_hexis = $this->hmac($regionName, $kDate, false);
        $kService_hexis = $this->hmac($SERVICE_NAME, $kRegion, false);


        $url = "https://".$endpoint . "/" . $serviceOperation;
        $headers = $this->buildHeaders($payload, $authorizationValue, $dateTimeString, $serviceTarget);
        $signaturePos = strpos($authorizationValue, "Signature=");
        if ($signaturePos == FALSE || $signaturePos + 10 >= strlen($authorizationValue)) {
            $signatureStr = "Malformed";
        } else {
            $signatureStr = substr($authorizationValue, $signaturePos + 10);
        }

        $client = new Client();
        $result = $client->request($url, $headers, $payload);
        echo  "
			       <b>Payload:</b><p>" . ($payload) . "</p>
			       <b>Hased Payload:</b><p>" . $this->buildHash($payload) . "</p>
			       <b>Canonical Request:</b><p>" . $this->convertNewline($canonicalRequest) . "</p> 
			       <b>Hashed Canonical Request:</b><p>" . $canonicalRequestHash . "</p> 
			       <b>key:</b><p>" . $secretKey . "</p>
			       <b>Secretkey:</b><p>" . $signatureAWSKey . "</p>
			       <b>Hashed Secretkey:</b><p>" . $this->buildHash($signatureAWSKey) . "</p>
			       <b>X-amz-date:</b><p>" . $dateTimeString . "</p>
			       <b>String To Sign:</b><p>" . $this->convertNewline($stringToSign) . "</p>
			       <b>Endpoint:</b><p>" . $endpoint . "</p>
			       <b>Region:</b><p>" . $regionName . "</p>
			       <b>Authorization:</b><p>" . $authorizationValue . "</p>
			       <b>kDate:</b><p>" . $kDate_hexis . "</p>
			       <b>kRegion:</b><p>" . $kRegion_hexis . "</p>
			       <b>kService:</b><p>" . $kService_hexis . "</p>
			       <b>kSigning:</b><p>" . $this->buildDerivedKey(false) . "</p>
			       <b>Signature:</b><p>" . $signatureStr . "</p>
			       <b>Signed Request:</b><p> 
			       POST /" . $serviceOperation . " HTTP/1.1" . "</br>" . $this->convertNewline($this->buildCanonicalHeaders($serviceOperation)) . "</br>
			       Authorization: " . $authorizationValue . "</br>" . htmlEntities($payload) . " </p>
			       </br>
			       <b> Response: </b><p>" . htmlspecialchars($result) . "</p>
			       </br>
			       </br>";
        return $result;
    }

    public function convertNewline($str)
    {
        $str = str_replace(array("\r\n", "\n", "\r"), '</br>', $str);
        return $str;
    }
    public function buildHeaders($payload, $authorizationValue, $dateTimeString, $serviceTarget)
    {
        $ACCEPT_HEADER = self::ACCEPT_HEADER;
        $X_AMZ_DATE_HEADER = self::X_AMZ_DATE_HEADER;
        $X_AMZ_TARGET_HEADER = self::X_AMZ_TARGET_HEADER;
        $AUTHORIZATION_HEADER = self::AUTHORIZATION_HEADER;
        return [
            'Content-Type:' . $this->getContentType(),
            'Content-Length: ' . strlen($payload),
            $AUTHORIZATION_HEADER . ":" . $authorizationValue,
            $X_AMZ_DATE_HEADER . ":" . $dateTimeString,
            $X_AMZ_TARGET_HEADER . ":" . $serviceTarget,
            $ACCEPT_HEADER . ":" . $this->getContentType()
        ];
    }

    public function buildAuthSignature($stringToSign)
    {
        $AWS_SHA256_ALGORITHM = self::AWS_SHA256_ALGORITHM;
        $SERVICE_NAME = self::SERVICE_NAME;
        $TERMINATION_STRING = self::TERMINATION_STRING;
        $ACCEPT_HEADER = self::ACCEPT_HEADER;
        $HOST_HEADER = self::HOST_HEADER;
        $X_AMZ_DATE_HEADER = self::X_AMZ_DATE_HEADER;
        $X_AMZ_TARGET_HEADER = self::X_AMZ_TARGET_HEADER;

        $awsKeyId = $this->config->getAccessKey();
        $regionName = $this->getRegion();

        $dateString = $this->getDateString();
        $derivedKey = $this->buildDerivedKey();
        // Calculate signature per http://docs.aws.amazon.com/general/latest/gr/sigv4-calculate-signature.html
        $finalSignature = $this->hmac($stringToSign, $derivedKey, false);

        // Assemble Authorization Header with signing information
        // per http://docs.aws.amazon.com/general/latest/gr/sigv4-add-signature-to-request.html
        $authorizationValue =
            $AWS_SHA256_ALGORITHM
            . " Credential=" . $awsKeyId
            . "/" . $dateString . "/" . $regionName . "/" . $SERVICE_NAME . "/" . $TERMINATION_STRING . ","
            . " SignedHeaders="
            . $ACCEPT_HEADER . ";" . $HOST_HEADER . ";" . $X_AMZ_DATE_HEADER . ";" . $X_AMZ_TARGET_HEADER . ","
            . " Signature="
            . $finalSignature;

        return $authorizationValue;
    }

    public function buildStringToSign($canonicalRequestHash)
    {
        $AWS_SHA256_ALGORITHM = self::AWS_SHA256_ALGORITHM;
        $TERMINATION_STRING = self::TERMINATION_STRING;
        $SERVICE_NAME = self::SERVICE_NAME;
        $regionName = $this->getRegion();
        $dateTimeString = $this->getTimeStamp();
        $dateString = $this->getDateString();
        $stringToSign = "$AWS_SHA256_ALGORITHM\n$dateTimeString\n$dateString/$regionName/$SERVICE_NAME/$TERMINATION_STRING\n$canonicalRequestHash";

        return $stringToSign;
    }

    public function buildDerivedKey($rawOutput = true)
    {
        $KEY_QUALIFIER = self::KEY_QUALIFIER;
        $TERMINATION_STRING = self::TERMINATION_STRING;
        $SERVICE_NAME = self::SERVICE_NAME;

        $awsSecretKey = $this->config->getSecret();
        // Append Key Qaulifier, "AWS4", to secret key per http://docs.aws.amazon.com/general/latest/gr/signature-v4-examples.html
        $signatureAWSKey = $KEY_QUALIFIER . $awsSecretKey;
        $regionName = $this->getRegion();
        $dateString = $this->getDateString();

        $kDate = $this->hmac($dateString, $signatureAWSKey);
        $kRegion = $this->hmac($regionName, $kDate);
        $kService = $this->hmac($SERVICE_NAME, $kRegion);

        // Derived the Signing key (derivedKey aka kSigning)
        $derivedKey = $this->hmac($TERMINATION_STRING, $kService, $rawOutput);
        return $derivedKey;
    }

    public function getRegion()
    {


        $endpoint = $this->config->getEndpoint();
        $regionName = "us-east-1";

        if ($endpoint == "agcod-v2-eu.amazon.com" || $endpoint == "agcod-v2-eu-gamma.amazon.com") {
            $regionName = "eu-west-1";
        } else if ($endpoint == "agcod-v2-fe.amazon.com" || $endpoint == "agcod-v2-fe-gamma.amazon.com") {
            $regionName = "us-west-2";
        }
        return $regionName;
    }


    public function getGiftCardPayload($amount)
    {
        $amount = trim($amount);
        $payload = array(
            "creationRequestId" => $this->config->getPartner() ."_". time(),
            "partnerId" => $this->config->getPartner(),
            "value" =>
                array(
                    "currencyCode" => $this->config->getCurrency(),
                    "amount" => floatval($amount)
                )
        );
        return json_encode($payload);
    }

    public function getCanonicalRequest($serviceOperation, $payload)
    {
        $HOST_HEADER = self::HOST_HEADER;
        $X_AMZ_DATE_HEADER = self::X_AMZ_DATE_HEADER;
        $X_AMZ_TARGET_HEADER = self::X_AMZ_TARGET_HEADER;
        $ACCEPT_HEADER = self::ACCEPT_HEADER;
        $payloadHash = $this->buildHash($payload);
        $canonicalHeaders = $this->buildCanonicalHeaders($serviceOperation);
        $canonicalRequest = "POST\n/$serviceOperation\n\n$canonicalHeaders\n\n$ACCEPT_HEADER;$HOST_HEADER;$X_AMZ_DATE_HEADER;$X_AMZ_TARGET_HEADER\n$payloadHash";
        return $canonicalRequest;
    }

    public function buildHash($data)
    {
        return hash("sha256", $data);
    }

    public function getTimestamp()
    {
        return gmdate('Ymd\THis\Z');
    }

    public function hmac($data, $key, $raw = true)
    {
        return hash_hmac("sha256", $data, $key, $raw);
    }

    public function getDateString()
    {
        return substr($this->getTimeStamp(), 0, 8);
    }

    public function getContentType()
    {
        return "application/json";
    }

    public function buildCanonicalHeaders($serviceOperation)
    {
        $ACCEPT_HEADER = self::ACCEPT_HEADER;
        $HOST_HEADER = self::HOST_HEADER;
        $X_AMZ_DATE_HEADER = self::X_AMZ_DATE_HEADER;
        $X_AMZ_TARGET_HEADER = self::X_AMZ_TARGET_HEADER;
        $dateTimeString = $this->getTimeStamp();
        $endpoint = $this->config->getEndpoint();
        $contentType = $this->getContentType();
        return
            "$ACCEPT_HEADER:$contentType\n$HOST_HEADER:$endpoint\n$X_AMZ_DATE_HEADER:$dateTimeString\n$X_AMZ_TARGET_HEADER:com.amazonaws.agcod.AGCODService.$serviceOperation";
    }
}