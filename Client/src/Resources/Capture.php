<?php

	// Mollie Shopware Plugin Version: 1.3.15

namespace Mollie\Api\Resources;

class Capture extends \Mollie\Api\Resources\BaseResource
{
    /**
     * Always 'capture' for this object
     * @var string
     */
    public $resource;
    /**
     * Id of the capture
     * @var string
     */
    public $id;
    /**
     * Mode of the capture, either "live" or "test" depending on the API Key that was used.
     *
     * @var string
     */
    public $mode;
    /**
     * Amount object containing the value and currency
     *
     * @var object
     */
    public $amount;
    /**
     * Amount object containing the settlement value and currency
     *
     * @var object
     */
    public $settlementAmount;
    /**
     * Id of the capture's payment (on the Mollie platform).
     *
     * @var string
     */
    public $paymentId;
    /**
     * Id of the capture's shipment (on the Mollie platform).
     *
     * @var string
     */
    public $shipmentId;
    /**
     * Id of the capture's settlement (on the Mollie platform).
     *
     * @var string
     */
    public $settlementId;
    /**
     * @var string
     */
    public $createdAt;
    /**
     * @var object
     */
    public $_links;
}