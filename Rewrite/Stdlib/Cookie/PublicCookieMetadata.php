<?php
declare(strict_types = 1);
namespace Veriteworks\CookieFix\Rewrite\Stdlib\Cookie;

use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata as Base;

/**
 * Class PublicCookieMetadata
 * for PublicCookieMetadata
 */
class PublicCookieMetadata extends Base
{
    const KEY_SAMESITE = 'same_site';
    /**
     * Set SameSite flag
     *
     * @param string $sameSite
     * @return \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata
     */
    public function setSameSite($sameSite)
    {
        return $this->set(self::KEY_SAMESITE, $sameSite);
    }
}
