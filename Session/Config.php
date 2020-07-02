<?php
namespace Veriteworks\CookieFix\Session;

use Magento\Framework\Session\Config as BaseConfig;

/**
 * Class Config
 * @package Veriteworks\CookieFix\Session
 */
class Config extends BaseConfig
{

    /**
     * Set session.cookie_path
     *
     * @param string $cookiePath
     * @param string|null $default
     * @return \Magento\Framework\Session\Config
     */
    public function setCookiePath($cookiePath, $default = null)
    {
        parent::setCookiePath($cookiePath, $default);

        $cookiePath = $this->getCookiePath();

        $version = PHP_VERSION_ID;

        if ($version >= 70300) {
            $this->setOption('session.cookie_samesite', 'None');
        } else {
            if (!preg_match('/SameSite/', $cookiePath)) {
                $cookiePath .= '; SameSite=None;';
                $this->setOption('session.cookie_path', $cookiePath);
            }
        }

        return $this;
    }
}
