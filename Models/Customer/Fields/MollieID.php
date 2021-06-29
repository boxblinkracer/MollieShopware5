<?php

namespace MollieShopware\Models\Customer\Fields;


class MollieID
{

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var string
     */
    private $liveId;

    /**
     * @var string
     */
    private $testId;

    /**
     * MollieID constructor.
     * @param int $shopId
     * @param string $liveId
     * @param string $testId
     */
    public function __construct($shopId, $liveId, $testId)
    {
        $this->shopId = $shopId;
        $this->liveId = $liveId;
        $this->testId = $testId;
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @return string
     */
    public function getLiveId()
    {
        return $this->liveId;
    }

    /**
     * @return string
     */
    public function getTestId()
    {
        return $this->testId;
    }

}
