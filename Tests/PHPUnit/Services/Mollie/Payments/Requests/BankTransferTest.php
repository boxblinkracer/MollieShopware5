<?php

namespace MollieShopware\Tests\Services\Mollie\Payments\Requests;

use MollieShopware\Services\Mollie\Payments\Models\Payment;
use MollieShopware\Services\Mollie\Payments\Models\PaymentAddress;
use MollieShopware\Services\Mollie\Payments\Models\PaymentLineItem;
use MollieShopware\Services\Mollie\Payments\Requests\BankTransfer;
use MollieShopware\Tests\Utils\Traits\PaymentTestTrait;
use PHPUnit\Framework\TestCase;


class BankTransferTest extends TestCase
{
    use PaymentTestTrait;


    /**
     * @var BankTransfer
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
        $this->payment = new BankTransfer();

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
     *
     * @covers  \MollieShopware\Services\Mollie\Payments\Requests\BankTransfer
     */
    public function testPaymentsAPI()
    {
        $expected = [
            'method' => 'banktransfer',
            'amount' => [
                'currency' => 'USD',
                'value' => '49.98',
            ],
            'description' => 'Payment UUID-123',
            'redirectUrl' => 'https://local/redirect',
            'webhookUrl' => 'https://local/notify',
            'locale' => 'de_DE',
            'billingEmail' => 'dev@mollie.local',
        ];

        $requestBody = $this->payment->buildBodyPaymentsAPI();

        $this->assertEquals($expected, $requestBody);
    }

    /**
     * This test verifies that the Orders-API request
     * for our payment is correct.
     *
     * @covers \MollieShopware\Services\Mollie\Payments\Requests\BankTransfer
     */
    public function testOrdersAPI()
    {
        $expected = [
            'method' => 'banktransfer',
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
     * This test verifies that our billing email is
     * correctly existing where necessary.
     * Please keep in mind, this must NOT exist in the orders API!
     *
     * @covers  \MollieShopware\Services\Mollie\Payments\Requests\BankTransfer
     */
    public function testBillingMail()
    {
        $paymentsAPI = $this->payment->buildBodyPaymentsAPI();
        $ordersAPI = $this->payment->buildBodyOrdersAPI();

        $this->assertEquals('dev@mollie.local', $paymentsAPI['billingEmail']);
        $this->assertEquals(false, isset($ordersAPI['payments']['billingEmail']));
    }

    /**
     * This test verifies that we can set a custom due date for our payment.
     * We set the due date in DAYS and it will automatically calculate
     * the date time for the request.
     *
     * @covers  \MollieShopware\Services\Mollie\Payments\Requests\BankTransfer::setDueDateDays
     */
    public function testDueDate()
    {
        $dueInDays = 5;

        $expectedDueDate = date('Y-m-d', strtotime(' + ' . $dueInDays . ' day'));

        $this->payment->setDueDateDays($dueInDays);

        $paymentsAPI = $this->payment->buildBodyPaymentsAPI();
        $ordersAPI = $this->payment->buildBodyOrdersAPI();

        $this->assertEquals($expectedDueDate, $paymentsAPI['dueDate']);
        $this->assertEquals($expectedDueDate, $ordersAPI['payment']['dueDate']);
    }

}
