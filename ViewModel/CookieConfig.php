<?php
namespace Veriteworks\CookieFix\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Veriteworks\CookieFix\Plugin\SwitchSameSite;

class CookieConfig implements ArgumentInterface
{
    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private ConfigInterface $sessionConfig
    ) {
    }

    public function getSameSite(): ?string
    {
        $sameSite = strtolower((string)$this->scopeConfig->getValue(
            SwitchSameSite::CONFIG_PATH,
            ScopeInterface::SCOPE_STORE
        ));

        if (!in_array($sameSite, ['none', 'strict'], true)) {
            return null;
        }

        if ($sameSite === 'none' && !$this->sessionConfig->getCookieSecure()) {
            return null;
        }

        return $sameSite;
    }
}
