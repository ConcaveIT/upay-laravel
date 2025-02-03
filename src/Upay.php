<?php

namespace Concaveit\Upay;

use GuzzleHttp\Client;
use Exception;

class Upay
{
    protected $client;
    protected $baseUrl;
    protected $merchantId;
    protected $merchantKey;

    public function __construct(array $config)
    {
        $this->baseUrl    = rtrim($config['base_url'], '/');
        $this->merchantId = $config['merchant_id'];
        $this->merchantKey= $config['merchant_key'];

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 10,
        ]);
    }

    /**
     * Authenticate the merchant and retrieve a token.
     *
     * @return string
     * @throws Exception
     */
    public function authenticate()
    {
        $endpoint = '/payment/merchant-auth/';
        $payload  = [
            'merchant_id'  => $this->merchantId,
            'merchant_key' => $this->merchantKey,
        ];

        $response = $this->client->post($endpoint, [
            'json' => $payload,
        ]);

        $result = json_decode($response->getBody(), true);

        if (isset($result['code']) && $result['code'] === 'MAS2001') {
            return $result['data']['token'];
        }

        throw new Exception('Authentication failed: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * Initialize a merchant payment.
     *
     * @param array $data Payment data as specified in the Upay docs.
     * @return array Payment initialization response data.
     * @throws Exception
     */
    public function initPayment(array $data)
    {
        $endpoint = '/payment/merchant-payment-init/';
        $token    = $this->authenticate();

        $headers = [
            'Authorization' => 'UPAY ' . $token,
        ];

        $response = $this->client->post($endpoint, [
            'headers' => $headers,
            'json'    => $data,
        ]);

        $result = json_decode($response->getBody(), true);

        if (isset($result['code']) && $result['code'] === 'MPIS2002') {
            return $result['data'];
        }

        throw new Exception('Payment initialization failed: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * Get the status of a single payment.
     *
     * @param string $txnId
     * @return array Payment status data.
     * @throws Exception
     */
    public function getPaymentStatus(string $txnId)
    {
        $token    = $this->authenticate();
        $endpoint = '/payment/single-payment-status/' . $txnId . '/';

        $headers = [
            'Authorization' => 'UPAY ' . $token,
        ];

        $response = $this->client->get($endpoint, [
            'headers' => $headers,
        ]);

        $result = json_decode($response->getBody(), true);

        if (isset($result['code']) && $result['code'] === 'PS2005') {
            return $result['data'];
        }

        throw new Exception('Payment status fetch failed: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * Get the status of multiple payments.
     *
     * @param array $txnIdList
     * @return array Bulk payment status data.
     * @throws Exception
     */
    public function getBulkPaymentStatus(array $txnIdList)
    {
        $token    = $this->authenticate();
        $endpoint = '/payment/bulk-payment-status/';

        $headers = [
            'Authorization' => 'UPAY ' . $token,
        ];

        $payload = [
            'txn_id_list' => $txnIdList,
        ];

        $response = $this->client->post($endpoint, [
            'headers' => $headers,
            'json'    => $payload,
        ]);

        $result = json_decode($response->getBody(), true);

        if (isset($result['code']) && $result['code'] === 'PS2005') {
            return $result['data'];
        }

        throw new Exception('Bulk payment status fetch failed: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * Process a bulk refund.
     *
     * @param array $refunds Array of refund objects (each with keys: txn_id, refund_amount).
     * @return array Refund response data.
     * @throws Exception
     */
    public function bulkRefund(array $refunds)
    {
        $token    = $this->authenticate();
        $endpoint = '/payment/bulk/refund/';

        $headers = [
            'Authorization' => 'UPAY ' . $token,
        ];

        $response = $this->client->post($endpoint, [
            'headers' => $headers,
            'json'    => $refunds,
        ]);

        $result = json_decode($response->getBody(), true);

        if (isset($result['code']) && $result['code'] === 'MPR_200') {
            return $result['data'];
        }

        throw new Exception('Bulk refund failed: ' . ($result['message'] ?? 'Unknown error'));
    }
}
