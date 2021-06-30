<?php

namespace MollieShopware\Tests\Utils\Fakes\Session;


use MollieShopware\Components\SessionManager\SessionManagerInterface;
use MollieShopware\Models\Transaction;


class FakeSessionManager implements SessionManagerInterface
{

    /**
     * @var string
     */
    private $sessionId;


    /**
     * @param string $sessionId
     */
    public function __construct(string $sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function extendSessionLifespan($days)
    {
    }

    public function generateSessionToken(Transaction $transaction)
    {
    }

    public function deleteSessionToken(Transaction $transaction)
    {
    }

    public function restoreFromToken(Transaction $transaction, $requestSessionToken)
    {
    }

}
