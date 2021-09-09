<?php

/**
 * Part of the AmazonGiftCode package.
 * Author: Kashyap Merai <kashyapk62@gmail.com>
 *
 */


namespace kamerk22\AmazonGiftCode\AWS;


use kamerk22\AmazonGiftCode\Client\Client;
use kamerk22\AmazonGiftCode\Config\Config;
use kamerk22\AmazonGiftCode\Exceptions\AmazonErrors;
use kamerk22\AmazonGiftCode\Response\CancelResponse;
use kamerk22\AmazonGiftCode\Response\CreateBalanceResponse;
use kamerk22\AmazonGiftCode\Response\CreateResponse;

class AWS
{
    public const SERVICE_NAME = 'AGCODService';
    public const ACCEPT_HEADER = 'accept';
    public const CONTENT_HEADER = 'content-type';
    public const HOST_HEADER = 'host';
    public const X_AMZ_DATE_HEADER = 'x-amz-date';
    public const X_AMZ_TARGET_HEADER = 'x-amz-target';
    public const AUTHORIZATION_HEADER = 'Authorization';
    public const AWS_SHA256_ALGORITHM = 'AWS4-HMAC-SHA256';
    public const KEY_QUALIFIER = 'AWS4';
    public const TERMINATION_STRING = 'aws4_request';
    public const CREATE_GIFT_CARD_SERVICE = 'CreateGiftCard';
    public const CANCEL_GIFT_CARD_SERVICE = 'CancelGiftCard';
    public const GET_AVAILABLE_FUNDS_SERVICE = 'GetAvailableFunds';

    private $_config;


    /**
     * AWS constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->_config = $config;
    }


    /**
     * @param $amount
     * @param $creationId
     * @return CreateResponse
     *
     * @throws AmazonErrors
     */
    public function getCode($amount, $creationId = null): CreateResponse
    {
        $serviceOperation = self::CREATE_GIFT_CARD_SERVICE;
        $payload = $this->getGiftCardPayload($amount, $creationId);
        $canonicalRequest = $this->getCanonicalRequest($serviceOperation, $payload);
        $dateTimeString = $this->getTimestamp();
        $result = json_decode($this->makeRequest($payload, $canonicalRequest, $serviceOperation, $dateTimeString), true);
        return new CreateResponse($result);

    }

    /**
     * @param $creationRequestId
     * @param $gcId
     * @return CancelResponse
     */
    public function cancelCode($creationRequestId, $gcId): CancelResponse
    {
        $serviceOperation = self::CANCEL_GIFT_CARD_SERVICE;
        $payload = $this->getCancelGiftCardPayload($creationRequestId, $gcId);
        $canonicalRequest = $this->getCanonicalRequest($serviceOperation, $payload);
        $dateTimeString = $this->getTimestamp();
        $result = json_decode($this->makeRequest($payload, $canonicalRequest, $serviceOperation, $dateTimeString), true);
        return new CancelResponse($result);
    }

    /**
     * @return CreateBalanceResponse
     */
    public function getBalance(): CreateBalanceResponse
    {
        $serviceOperation = self::GET_AVAILABLE_FUNDS_SERVICE;
        $payload = $this->getAvailableFundsPayload();
        $canonicalRequest = $this->getCanonicalRequest($serviceOperation, $payload);
        $dateTimeString = $this->getTimestamp();
        $result = json_decode($this->makeRequest($payload, $canonicalRequest, $serviceOperation, $dateTimeString), true);
        return new CreateBalanceResponse($result);
    }

    /**
     * @param $payload
     * @param $canonicalRequest
     * @param $serviceOperation
     * @param $dateTimeString
     * @return String
     */
    public function makeRequest($payload, $canonicalRequest, $serviceOperation, $dateTimeString): string
    {
        $KEY_QUALIFIER = self::KEY_QUALIFIER;
        $canonicalRequestHash = $this->buildHash($canonicalRequest);
        $stringToSign = $this->buildStringToSign($canonicalRequestHash);
        $authorizationValue = $this->buildAuthSignature($stringToSign);

        $secretKey = $this->_config->getSecret();
        $endpoint = $this->_config->getEndpoint();
        $regionName = $this->getRegion();

        $SERVICE_NAME = 'AGCODService';
        $serviceTarget = 'com.amazonaws.agcod.' . $SERVICE_NAME . '.' . $serviceOperation;
        $dateString = $this->getDateString();

        $signatureAWSKey = $KEY_QUALIFIER . $secretKey;

        $kDate = $this->hmac($dateString, $signatureAWSKey);
        $kDate_hexis = $this->hmac($dateString, $signatureAWSKey, false);
        $kRegion = $this->hmac($regionName, $kDate);
        $kRegion_hexis = $this->hmac($regionName, $kDate, false);
        $kService_hexis = $this->hmac($SERVICE_NAME, $kRegion, false);

        $url = 'https://' . $endpoint . '/' . $serviceOperation;
        $headers = $this->buildHeaders($payload, $authorizationValue, $dateTimeString, $serviceTarget);
        return (new Client())->request($url, $headers, $payload);
    }

    /**
     * @param $payload
     * @param $authorizationValue
     * @param $dateTimeString
     * @param $serviceTarget
     * @return array
     */
    public function buildHeaders($payload, $authorizationValue, $dateTimeString, $serviceTarget): array
    {
        $ACCEPT_HEADER = self::ACCEPT_HEADER;
        $X_AMZ_DATE_HEADER = self::X_AMZ_DATE_HEADER;
        $X_AMZ_TARGET_HEADER = self::X_AMZ_TARGET_HEADER;
        $AUTHORIZATION_HEADER = self::AUTHORIZATION_HEADER;
        return [
            'Content-Type:' . $this->getContentType(),
            'Content-Length: ' . strlen($payload),
            $AUTHORIZATION_HEADER . ':' . $authorizationValue,
            $X_AMZ_DATE_HEADER . ':' . $dateTimeString,
            $X_AMZ_TARGET_HEADER . ':' . $serviceTarget,
            $ACCEPT_HEADER . ':' . $this->getContentType()
        ];
    }

    /**
     * @param $stringToSign
     * @return string
     */
    public function buildAuthSignature($stringToSign): string
    {
        $AWS_SHA256_ALGORITHM = self::AWS_SHA256_ALGORITHM;
        $SERVICE_NAME = self::SERVICE_NAME;
        $TERMINATION_STRING = self::TERMINATION_STRING;
        $ACCEPT_HEADER = self::ACCEPT_HEADER;
        $HOST_HEADER = self::HOST_HEADER;
        $X_AMZ_DATE_HEADER = self::X_AMZ_DATE_HEADER;
        $X_AMZ_TARGET_HEADER = self::X_AMZ_TARGET_HEADER;

        $awsKeyId = $this->_config->getAccessKey();
        $regionName = $this->getRegion();

        $dateString = $this->getDateString();
        $derivedKey = $this->buildDerivedKey();
        // Calculate signature per http://docs.aws.amazon.com/general/latest/gr/sigv4-calculate-signature.html
        $finalSignature = $this->hmac($stringToSign, $derivedKey, false);

        // Assemble Authorization Header with signing information
        // per http://docs.aws.amazon.com/general/latest/gr/sigv4-add-signature-to-request.html
        $authorizationValue =
            $AWS_SHA256_ALGORITHM
            . ' Credential=' . $awsKeyId
            . '/' . $dateString . '/' . $regionName . '/' . $SERVICE_NAME . '/' . $TERMINATION_STRING . ','
            . ' SignedHeaders='
            . $ACCEPT_HEADER . ';' . $HOST_HEADER . ';' . $X_AMZ_DATE_HEADER . ';' . $X_AMZ_TARGET_HEADER . ','
            . ' Signature='
            . $finalSignature;

        return $authorizationValue;
    }

    /**
     * @param $canonicalRequestHash
     * @return string
     */
    public function buildStringToSign($canonicalRequestHash): string
    {
        $AWS_SHA256_ALGORITHM = self::AWS_SHA256_ALGORITHM;
        $TERMINATION_STRING = self::TERMINATION_STRING;
        $SERVICE_NAME = self::SERVICE_NAME;
        $regionName = $this->getRegion();
        $dateTimeString = $this->getTimestamp();
        $dateString = $this->getDateString();
        $stringToSign = "$AWS_SHA256_ALGORITHM\n$dateTimeString\n$dateString/$regionName/$SERVICE_NAME/$TERMINATION_STRING\n$canonicalRequestHash";

        return $stringToSign;
    }

    /**
     * @param bool $rawOutput
     * @return string
     */
    public function buildDerivedKey($rawOutput = true): string
    {
        $KEY_QUALIFIER = self::KEY_QUALIFIER;
        $TERMINATION_STRING = self::TERMINATION_STRING;
        $SERVICE_NAME = self::SERVICE_NAME;

        $awsSecretKey = $this->_config->getSecret();
        // Append Key Qualifier, "AWS4", to secret key per http://docs.aws.amazon.com/general/latest/gr/signature-v4-examples.html
        $signatureAWSKey = $KEY_QUALIFIER . $awsSecretKey;
        $regionName = $this->getRegion();
        $dateString = $this->getDateString();

        $kDate = $this->hmac($dateString, $signatureAWSKey);
        $kRegion = $this->hmac($regionName, $kDate);
        $kService = $this->hmac($SERVICE_NAME, $kRegion);

        // Derived the Signing key (derivedKey aka kSigning)
        return $this->hmac($TERMINATION_STRING, $kService, $rawOutput);
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        $endpoint = $this->_config->getEndpoint();
        $regionName = 'us-east-1';

        if ($endpoint === 'agcod-v2-eu.amazon.com' || $endpoint === 'agcod-v2-eu-gamma.amazon.com') {
            $regionName = 'eu-west-1';
        } else if ($endpoint === 'agcod-v2-fe.amazon.com' || $endpoint === 'agcod-v2-fe-gamma.amazon.com') {
            $regionName = 'us-west-2';
        }
        return $regionName;
    }


    /**
     * @param $amount
     * @param $creationId
     * @return string
     */
    public function getGiftCardPayload($amount, $creationId = null): string
    {
        $amount = trim($amount);
        $payload = [
            'creationRequestId' => $creationId ?: uniqid($this->_config->getPartner().'_'),
            'partnerId' => $this->_config->getPartner(),
            'value' =>
                [
                    'currencyCode' => $this->_config->getCurrency(),
                    'amount' => (float)$amount
                ]
        ];
        return json_encode($payload);
    }

    /**
     * @param $creationRequestId
     * @param $gcId
     * @return string
     */
    public function getCancelGiftCardPayload($creationRequestId, $gcId): string
    {
        $gcResponseId = trim($gcId);
        $payload = [
            'creationRequestId' => $creationRequestId,
            'partnerId' => $this->_config->getPartner(),
            'gcId' => $gcResponseId
        ];
        return json_encode($payload);
    }

    /**
     * @return string
     */
    public function getAvailableFundsPayload(): string
    {
        $payload = [
            'partnerId' => $this->_config->getPartner(),
        ];
        return json_encode($payload);
    }

    /**
     * @param $serviceOperation
     * @param $payload
     * @return string
     */
    public function getCanonicalRequest($serviceOperation, $payload): string
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

    /**
     * @param $data
     * @return string
     */
    public function buildHash($data): string
    {
        return hash('sha256', $data);
    }

    /**
     * @return false|string
     */
    public function getTimestamp()
    {
        return gmdate('Ymd\THis\Z');
    }

    /**
     * @param $data
     * @param $key
     * @param bool $raw
     * @return string
     */
    public function hmac($data, $key, $raw = true): string
    {
        return hash_hmac('sha256', $data, $key, $raw);
    }

    /**
     * @return bool|string
     */
    public function getDateString()
    {
        return substr($this->getTimestamp(), 0, 8);
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'application/json';
    }

    /**
     * @param $serviceOperation
     * @return string
     */
    public function buildCanonicalHeaders($serviceOperation): string
    {
        $ACCEPT_HEADER = self::ACCEPT_HEADER;
        $HOST_HEADER = self::HOST_HEADER;
        $X_AMZ_DATE_HEADER = self::X_AMZ_DATE_HEADER;
        $X_AMZ_TARGET_HEADER = self::X_AMZ_TARGET_HEADER;
        $dateTimeString = $this->getTimestamp();
        $endpoint = $this->_config->getEndpoint();
        $contentType = $this->getContentType();
        return
            "$ACCEPT_HEADER:$contentType\n$HOST_HEADER:$endpoint\n$X_AMZ_DATE_HEADER:$dateTimeString\n$X_AMZ_TARGET_HEADER:com.amazonaws.agcod.AGCODService.$serviceOperation";
    }
}
