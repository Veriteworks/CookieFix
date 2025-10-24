<?php

namespace Veriteworks\CookieFix\Plugin\Session;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\HTTP\Header;
use Veriteworks\CookieFix\Validator\SameSite;

class ModifySameSite
{
    public const CONFIG_PATH = 'web/cookie/samesite';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var SameSite
     */
    private $validator;
    /**
     * @var Header
     */
    private $header;

    /**
     * constructor
     *
     * @param Header $header
     * @param ScopeConfigInterface $scopeConfig
     * @param SameSite $validator
     */
    public function __construct(
        Header $header,
        ScopeConfigInterface $scopeConfig,
        SameSite $validator
    ) {
        $this->validator = $validator;
        $this->header = $header;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Modify samesite attribute
     *
     * @param ConfigInterface $subject
     * @param string $cookieSameSite
     * @return array
     */
    public function beforeSetCookieSameSite(ConfigInterface $subject, string $cookieSameSite = 'Lax'): array
    {
        if (!$subject->getCookieSecure()) {
            return [$cookieSameSite];
        }
        
        $agent = $this->header->getHttpUserAgent();
        $sameSite = $this->validator->shouldSendSameSiteNone($agent);
        if ($sameSite === false) {
            $cookieSameSite = 'None';
        } else {
            $config = $this->scopeConfig->getValue(self::CONFIG_PATH, ScopeInterface::SCOPE_STORE);
            if ($config !== $cookieSameSite) {
                $cookieSameSite = ucfirst($config);
            }
        }

        return [$cookieSameSite];
    }
}
