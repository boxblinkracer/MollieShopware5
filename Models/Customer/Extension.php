<?php

namespace MollieShopware\Models\Customer;


use MollieShopware\Models\Customer\Fields\MollieID;
use Shopware\Models\Customer\Customer;

class Extension
{

    /**
     * @var Customer
     */
    private $customer;


    /**
     * Extension constructor.
     * @param Customer $customer
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return MollieID[]
     */
    public function getMollieIDs()
    {
        $ids = [];

        $att = $this->customer->getAttribute();

        $shopSettings = json_decode($att->getMollieCustomerIds(), true);

        if ($shopSettings === null) {
            $shopSettings = [];
        }

        foreach ($shopSettings as $shopId => $values) {

            $ids[] = new MollieID(
                (int)$shopId,
                (string)$values['live'],
                (string)$values['test']
            );
        }

        return $ids;
    }

    /**
     * @param $shopId
     * @return MollieID|null
     */
    public function getMollieID($shopId)
    {
        foreach ($this->getMollieIDs() as $mollieID) {

            if ($mollieID->getShopId() === $shopId) {
                return $mollieID;
            }
        }

        return null;
    }

    /**
     * @param $shopId
     * @param $customerId
     * @param $isTestMode
     */
    public function setMollieId($shopId, $customerId, $isTestMode)
    {
        $allConfigs = $this->getMollieIDs();

        $json = [];

        foreach ($allConfigs as $mollieId) {

            $json[$mollieId->getShopId()] = [
                'test' => $mollieId->getTestId(),
                'live' => $mollieId->getLiveId(),
            ];
        };

        # now just merge our data with existing entries
        # by simply adding or overwriting the existing shop ID key.
        $existingConfig = $this->getMollieID($shopId);

        if ($existingConfig instanceof MollieID) {

            $json[$shopId] = [
                'test' => ($isTestMode) ? $customerId : $existingConfig->getTestId(),
                'live' => (!$isTestMode) ? $customerId : $existingConfig->getLiveId(),
            ];

        } else {

            $json[$shopId] = [
                'test' => ($isTestMode) ? $customerId : '',
                'live' => (!$isTestMode) ? $customerId : '',
            ];
        }

        $att = $this->customer->getAttribute();
        $att->setMollieCustomerIds(json_encode($json));
    }

}
