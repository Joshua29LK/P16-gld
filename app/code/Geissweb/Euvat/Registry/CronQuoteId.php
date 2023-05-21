<?php

namespace Geissweb\Euvat\Registry;

class CronQuoteId
{
    /**
     * @var int
     */
    private $quoteId;

    public function set(int $quoteId): void
    {
        $this->quoteId = $quoteId;
    }

    public function get(): ?int
    {
        return $this->quoteId;
    }
}
