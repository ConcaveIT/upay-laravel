# Upay Laravel Package

This package provides a convenient integration with the Upay Payment Gateway for your Laravel applications. With this package, you can authenticate your merchant, initialize payments, check payment statuses (both single and bulk), and process bulk refunds—all through a simple API.


## Installation

1. **Require the Package via Composer**

   Run the following command in your Laravel application's root directory:

   ```bash
   composer require concaveit/upay-laravel
   ```

   *Replace `concaveit` with your actual vendor name.*

2. **Publish the Configuration File**

   Publish the configuration file to your application's `config` directory by running:

   ```bash
   php artisan vendor:publish --tag=config
   ```

   This command will copy the `upay.php` configuration file into your Laravel project's `config` folder.

3. **Set Environment Variables**

   Open your `.env` file and add your Upay credentials:

   ```dotenv
   UPAY_BASE_URL=https://uat-pg.upay.systems
   UPAY_MERCHANT_ID=your-merchant-id
   UPAY_MERCHANT_KEY=your-merchant-key
   ```

## Configuration

The configuration file `config/upay.php` holds your Upay API settings. You can modify these settings directly or use environment variables to override them.

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Upay Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Upay credentials and base URL. These will be
    | used by the package to interact with the Upay Payment Gateway API.
    |
    */
    'base_url'    => env('UPAY_BASE_URL', 'https://uat-pg.upay.systems'),
    'merchant_id' => env('UPAY_MERCHANT_ID', ''),
    'merchant_key'=> env('UPAY_MERCHANT_KEY', ''),
];
```

## Usage

You can interact with the package either via its Facade or through Dependency Injection.

### Using the Facade

The package registers a facade named `Upay` that allows you to easily call its methods. For example, to initialize a payment:

```php
use Upay;

$data = [
    'date'                      => '2020-12-08',
    'txn_id'                    => 'Upay987654321',
    'invoice_id'                => 'Upay987654321',
    'amount'                    => 2050.00,
    'merchant_id'               => config('upay.merchant_id'),
    'merchant_name'             => 'rokomari',
    'merchant_code'             => '1252',
    'merchant_country_code'     => 'BD',
    'merchant_city'             => 'Dhaka',
    'merchant_category_code'    => '1252',
    'merchant_mobile'           => '01756348921',
    'transaction_currency_code' => 'BDT',
    'redirect_url'              => 'https://www.rokomari.com/checkout/redirect/',
    'additional_info'           => ['data' => 'example'],
    'is_cashback'               => false,
    'cashback_amount'           => 205.00,
    'cashback_wallet'           => '01756348921',
    'seat_count'                => '2'
];

try {
    $paymentInitResponse = Upay::initPayment($data);
    // Process the $paymentInitResponse as needed...
} catch (Exception $e) {
    // Handle errors appropriately
    dd($e->getMessage());
}
```

### Using Dependency Injection

You can also inject the Upay service directly into your classes. For example, in a controller:

```php
use Concaveit\Upay\Upay;

class PaymentController extends Controller
{
    protected $upay;

    public function __construct(Upay $upay)
    {
        $this->upay = $upay;
    }

    public function initPayment()
    {
        $data = [
            // Your payment data here...
        ];

        try {
            $response = $this->upay->initPayment($data);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

## Available Methods

- **authenticate()**  
  Authenticates the merchant credentials and retrieves an access token.

- **initPayment(array $data)**  
  Initializes a merchant payment. Accepts an array of payment data as specified in the Upay documentation.

- **getPaymentStatus(string $txnId)**  
  Retrieves the status of a single payment using the transaction ID.

- **getBulkPaymentStatus(array $txnIdList)**  
  Retrieves the status for multiple payments by accepting an array of transaction IDs.

- **bulkRefund(array $refunds)**  
  Processes refunds for multiple transactions. Accepts an array of refund objects, each containing a transaction ID and refund amount.

## File Structure

The package is structured as follows:

```
upay-laravel/
├── composer.json
├── config/
│   └── upay.php
└── src/
    ├── Facades/
    │   └── Upay.php
    ├── Upay.php
    └── UpayServiceProvider.php
```