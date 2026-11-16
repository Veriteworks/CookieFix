<?php
namespace Veriteworks\CookieFix\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Store\Model\ScopeInterface;
use Veriteworks\CookieFix\Validator\SameSite;

class SwitchSameSite
{
    public const CONFIG_PATH = 'web/cookie/samesite';
    public const CONFIG_AFFECTED_KEYS = 'web/cookie/affected_keys';
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

    /**
     * @var CookieMetadataFactory|null
     */
    private $metadataFactory;

    /**
     * @var array
     */
    private $affectedKeys = [];

    /**
     * SwitchSameSite constructor.
     *
     * @param Header $header
     * @param ScopeConfigInterface $scopeConfig
     * @param SameSite $validator
     * @param CookieMetadataFactory|null $metadataFactory
     */
    public function __construct(
        Header $header,
        ScopeConfigInterface $scopeConfig,
        SameSite $validator,
        ?CookieMetadataFactory $metadataFactory = null
    ) {
        $this->validator = $validator;
        $this->header = $header;
        $this->scopeConfig = $scopeConfig;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * Modify same site cookie attribute
     *
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
        ?PublicCookieMetadata $metadata = null
    ) {
        if ($this->isAffectedKeys($name)) {
            $agent = $this->header->getHttpUserAgent();
            $config = (string)$this->scopeConfig->getValue(self::CONFIG_PATH, ScopeInterface::SCOPE_STORE);
            if ($config !== '' && $this->validator->shouldSendSameSiteNone($agent)) {
                $metadata = $metadata ?? $this->getMetadataFactory()->createPublicCookieMetadata();

                // Convert to lowercase since sometimes it comes as lower-cased string
                if (strtolower($config) === 'none') {
                    $metadata->setSecure(true);
                }
                $metadata->setSameSite($config);
            }
        }

        return [$name, $value, $metadata];
    }

    /**
     * @return CookieMetadataFactory
     */
    private function getMetadataFactory(): CookieMetadataFactory
    {
        if ($this->metadataFactory === null) {
            $this->metadataFactory = ObjectManager::getInstance()->get(CookieMetadataFactory::class);
        }

        return $this->metadataFactory;
    }

    /**
     * Check given key is in affected keys list
     *
     * @param string $name
     * @return bool
     */
    private function isAffectedKeys($name)
    {
        if (!count($this->affectedKeys)) {
            $affectedKeys = $this->scopeConfig->getValue(self::CONFIG_AFFECTED_KEYS, ScopeInterface::SCOPE_STORE);
            $this->affectedKeys = explode(',', strtolower((string)$affectedKeys));
        }

        return in_array(strtolower($name), $this->affectedKeys);
    }
}
