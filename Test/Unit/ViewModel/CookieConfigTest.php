<?php
namespace Veriteworks\CookieFix\Test\Unit\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use Veriteworks\CookieFix\Plugin\SwitchSameSite;
use Veriteworks\CookieFix\ViewModel\CookieConfig;

class CookieConfigTest extends TestCase
{
    private function createViewModel(?string $configured, bool $secure): CookieConfig
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(SwitchSameSite::CONFIG_PATH, ScopeInterface::SCOPE_STORE)
            ->willReturn($configured);
        $sessionConfig = $this->createStub(ConfigInterface::class);
        $sessionConfig->method('getCookieSecure')->willReturn($secure);

        return new CookieConfig($scopeConfig, $sessionConfig);
    }

    public function testSecureNoneIsLowercased(): void
    {
        foreach (['None', 'none', 'NONE', 'nOnE'] as $configured) {
            $this->assertSame('none', $this->createViewModel($configured, true)->getSameSite());
        }
    }

    public function testInsecureNoneIsOmitted(): void
    {
        $this->assertNull($this->createViewModel('None', false)->getSameSite());
    }

    public function testLaxIsOmittedRegardlessOfCaseAndSecurity(): void
    {
        foreach (['Lax', 'lax', 'LAX', 'lAx'] as $configured) {
            foreach ([true, false] as $secure) {
                $this->assertNull($this->createViewModel($configured, $secure)->getSameSite());
            }
        }
    }

    public function testStrictIsLowercasedRegardlessOfSecurity(): void
    {
        foreach (['Strict', 'strict', 'STRICT', 'sTrIcT'] as $configured) {
            foreach ([true, false] as $secure) {
                $this->assertSame(
                    strtolower($configured),
                    $this->createViewModel($configured, $secure)->getSameSite()
                );
            }
        }
    }

    public function testEmptyAndUnknownValuesAreOmitted(): void
    {
        foreach ([null, '', 'unknown', ' None ', '0'] as $configured) {
            $this->assertNull($this->createViewModel($configured, true)->getSameSite());
        }
    }
}
