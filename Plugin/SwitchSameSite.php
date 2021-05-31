<?php
namespace Veriteworks\CookieFix\Plugin;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Store\Model\ScopeInterface;
use Veriteworks\CookieFix\Validator\SameSite;

class SwitchSameSite
{
    const CONFIG_PATH = 'web/cookie/samesite';
    const CONFIG_AFFECTED_KEYS = 'web/cookie/affected_keys';
    /**
     * @var SameSite
     */
    private $validator;
    /**
     * @var Header
     */
    private $header;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    private $affectedKeys = [];

    /**
     * SwitchSameSite constructor.
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
     * @param PhpCookieManager $subject
     * @param string $name
     * @param string $value
     * @param PublicCookieMetadata|null $metadata
     * @return array
     */
    public function beforeSetPublicCookie(
        PhpCookieManager $subject,
        $name,
        $value,
        PublicCookieMetadata $metadata = null
    ) {
        if ($this->isAffectedKeys($name)) {
            $agent = $this->header->getHttpUserAgent();
            $sameSite = $this->validator->shouldSendSameSiteNone($agent);
            if ($sameSite === false) {
                $metadata
                    ->setSecure(true)
                    ->setSameSite('None');
            } else {
                $config = $this->scopeConfig->getValue(self::CONFIG_PATH, ScopeInterface::SCOPE_STORE);

                // Convert to lowercase since sometimes it comes as lower-cased string
                if(strtolower($config) === 'none')
                {
                    $metadata->setSecure(true);
                }
                $metadata->setSameSite($config);
            }
        }

        return [$name, $value, $metadata];
    }

    private function isAffectedKeys($name)
    {
        if (!count($this->affectedKeys)) {
            $affectedKeys = $this->scopeConfig->getValue(self::CONFIG_AFFECTED_KEYS, ScopeInterface::SCOPE_STORE);
            $this->affectedKeys = explode(',', strtolower($affectedKeys));
        }

        return in_array(strtolower($name), $this->affectedKeys);
    }
}
