<?php

namespace MollieShopware\Tests\Services\Mollie\Payments\Requests;

use MollieShopware\Services\Mollie\Payments\Models\Payment;
use MollieShopware\Services\Mollie\Payments\Models\PaymentAddress;
use MollieShopware\Services\Mollie\Payments\Models\PaymentLineItem;
use MollieShopware\Services\Mollie\Payments\Requests\Belfius;
use MollieShopware\Tests\Utils\Traits\PaymentTestTrait;
use PHPUnit\Framework\TestCase;


class BelfiusTest extends TestCase
{
    use PaymentTestTrait;


    /**
     * @var Belfius
     */
    private $payment;

    /**
     * @var PaymentAddress
     */
    private $address;

    /**
     * @var PaymentLineItem
     */
    private $lineItem;

    /**
     *
     */
    public function setUp(): void
    {
        $this->payment = new Belfius();

        $this->address = $this->getAddressFixture();
        $this->lineItem = $this->getLineItemFixture();

        $this->payment->setPayment(
            new Payment(
                'UUID-123',
                'Payment UUID-123',
                '20004',
                $this->address,
                $this->address,
                49.98,
                [$this->lineItem],
                'USD',
                'de_DE',
                'https://local/redirect',
                'https://local/notify'
            )
        );
    }

    /**
     * This test verifies that the Payments-API request
     * for our payment is correct.
     */
    public function testPaymentsAPI()
    {
        $expected = [
            'method' => 'belfius',
            'amount' => [
                'currency' => 'USD',
                'value' => '49.98',
            ],
            'description' => 'Payment UUID-123',
            'redirectUrl' => 'https://local/redirect',
            'webhookUrl' => 'https://local/notify',
            'locale' => 'de_DE',
        ];

        $requestBody = $this->payment->buildBodyPaymentsAPI();

        $this->assertEquals($expected, $requestBody);
    }

    /**
     * This test verifies that the Orders-API request
     * for our payment is correct.
     */
    public function testOrdersAPI()
    {
        $expected = [
            'method' => 'belfius',
            'amount' => [
                'currency' => 'USD',
                'value' => '49.98',
            ],
            'redirectUrl' => 'https://local/redirect',
            'webhookUrl' => 'https://local/notify',
            'locale' => 'de_DE',
            'orderNumber' => '20004',
            'payment' => [
                'webhookUrl' => 'https://local/notify',
            ],
            'billingAddress' => $this->getExpectedAddressStructure($this->address),
            'shippingAddress' => $this->getExpectedAddressStructure($this->address),
            'lines' => [
                $this->getExpectedLineItemStructure($this->lineItem),
            ],
            'metadata' => [],
        ];

        $requestBody = $this->payment->buildBodyOrdersAPI();

        $this->assertSame($expected, $requestBody);
    }

    /**
     * This test verifies that we can set a custom expiration date
     * for our Orders API request.
     */
    public function testExpirationDate()
    {
        $dueInDays = 5;
        $expectedDueDate = date('Y-m-d', strtotime(' + ' . $dueInDays . ' day'));

        $this->payment->setExpirationDays($dueInDays);
        $request = $this->payment->buildBodyOrdersAPI();

        $this->assertEquals($expectedDueDate, $request['expiresAt']);
    }

    /**
     * This test verifies that we can set a customer id
     * for both types of requests
     */
    public function testCustomerId()
    {
        $this->payment->setCustomerId('cust_123');

        $paymentsRequest = $this->payment->buildBodyPaymentsAPI();
        $ordersRequest = $this->payment->buildBodyOrdersAPI();

        $this->assertEquals('cust_123', $paymentsRequest['customerId']);
        $this->assertEquals('cust_123', $ordersRequest['payment']['customerId']);
    }

}
