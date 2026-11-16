<?php
namespace Veriteworks\CookieFix\Test\Unit\Plugin\Session;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Session\Config\ConfigInterface;
use PHPUnit\Framework\TestCase;
use Veriteworks\CookieFix\Plugin\Session\ModifySameSite;
use Veriteworks\CookieFix\Validator\SameSite;

class ModifySameSiteTest extends TestCase
{
    private const COMPATIBLE_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36' .
        ' (KHTML, like Gecko) Chrome/80.0.3729.157 Safari/537.36';
    private const IOS12_AGENT = 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_4_1 like Mac OS X)' .
        ' AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1';

    /**
     * @param string $agent
     * @param string $configured
     * @return ModifySameSite
     */
    private function createPlugin(string $agent, string $configured): ModifySameSite
    {
        $header = $this->createStub(Header::class);
        $header->method('getHttpUserAgent')->willReturn($agent);
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn($configured);

        return new ModifySameSite($header, $scopeConfig, new SameSite());
    }

    /**
     * @param bool $secure
     * @return ConfigInterface
     */
    private function createSessionConfig(bool $secure = true): ConfigInterface
    {
        $config = $this->createStub(ConfigInterface::class);
        $config->method('getCookieSecure')->willReturn($secure);

        return $config;
    }

    public function testCompatibleAgentReceivesConfiguredNone(): void
    {
        $plugin = $this->createPlugin(self::COMPATIBLE_AGENT, 'none');

        $this->assertSame(
            ['None'],
            $plugin->beforeSetCookieSameSite($this->createSessionConfig(), 'Lax')
        );
    }

    public function testCompatibleAgentReceivesConfiguredLax(): void
    {
        $plugin = $this->createPlugin(self::COMPATIBLE_AGENT, 'Lax');

        $this->assertSame(
            ['Lax'],
            $plugin->beforeSetCookieSameSite($this->createSessionConfig(), 'Lax')
        );
    }

    public function testIncompatibleAgentNeverReceivesNone(): void
    {
        $plugin = $this->createPlugin(self::IOS12_AGENT, 'none');

        $this->assertSame(
            ['Lax'],
            $plugin->beforeSetCookieSameSite($this->createSessionConfig(), 'Lax')
        );
    }

    public function testInsecureSessionIsUntouched(): void
    {
        $plugin = $this->createPlugin(self::COMPATIBLE_AGENT, 'none');

        $this->assertSame(
            ['Lax'],
            $plugin->beforeSetCookieSameSite($this->createSessionConfig(false), 'Lax')
        );
    }

    public function testEmptyConfigIsUntouched(): void
    {
        $plugin = $this->createPlugin(self::COMPATIBLE_AGENT, '');

        $this->assertSame(
            ['Lax'],
            $plugin->beforeSetCookieSameSite($this->createSessionConfig(), 'Lax')
        );
    }
}
