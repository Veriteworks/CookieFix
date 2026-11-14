<?php
namespace Veriteworks\CookieFix\Test\Unit\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use PHPUnit\Framework\MockObject\Stub;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use Veriteworks\CookieFix\Plugin\SwitchSameSite;
use Veriteworks\CookieFix\Validator\SameSite;

class SwitchSameSiteTest extends TestCase
{
    private const COMPATIBLE_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36' .
        ' (KHTML, like Gecko) Chrome/80.0.3729.157 Safari/537.36';
    private const IOS12_AGENT = 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_4_1 like Mac OS X)' .
        ' AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1';

    /**
     * @var Header|Stub
     */
    private $header;

    /**
     * @var CookieMetadataFactory|Stub
     */
    private $metadataFactory;

    /**
     * @var SwitchSameSite
     */
    private $plugin;

    public function setUp(): void
    {
        $this->header = $this->createStub(Header::class);
        $this->metadataFactory = $this->createStub(CookieMetadataFactory::class);
        $this->metadataFactory->method('createPublicCookieMetadata')
            ->willReturnCallback(static fn() => new PublicCookieMetadata());
    }

    /**
     * @param string $configured
     */
    private function configure(string $configured): void
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnMap([
            [SwitchSameSite::CONFIG_AFFECTED_KEYS, ScopeInterface::SCOPE_STORE, null, 'form_key'],
            [SwitchSameSite::CONFIG_PATH, ScopeInterface::SCOPE_STORE, null, $configured],
        ]);
        $this->plugin = new SwitchSameSite(
            $this->header,
            $scopeConfig,
            new SameSite(),
            $this->metadataFactory
        );
    }

    public function testCompatibleAgentReceivesConfiguredNone(): void
    {
        $this->configure('None');
        $this->header->method('getHttpUserAgent')->willReturn(self::COMPATIBLE_AGENT);
        $metadata = new PublicCookieMetadata();

        [, , $result] = $this->plugin->beforeSetPublicCookie(
            $this->createStub(PhpCookieManager::class),
            'form_key',
            'value',
            $metadata
        );

        $this->assertSame('None', $result->getSameSite());
        $this->assertTrue($result->getSecure());
    }

    public function testCompatibleAgentReceivesConfiguredLax(): void
    {
        $this->configure('Lax');
        $this->header->method('getHttpUserAgent')->willReturn(self::COMPATIBLE_AGENT);

        [, , $result] = $this->plugin->beforeSetPublicCookie(
            $this->createStub(PhpCookieManager::class),
            'form_key',
            'value',
            new PublicCookieMetadata()
        );

        $this->assertSame('Lax', $result->getSameSite());
    }

    public function testIncompatibleAgentNeverReceivesNone(): void
    {
        $this->configure('None');
        $this->header->method('getHttpUserAgent')->willReturn(self::IOS12_AGENT);
        $metadata = new PublicCookieMetadata();

        [, , $result] = $this->plugin->beforeSetPublicCookie(
            $this->createStub(PhpCookieManager::class),
            'form_key',
            'value',
            $metadata
        );

        $this->assertSame($metadata, $result);
        $this->assertSame('Lax', $result->getSameSite());
        $this->assertNull($result->getSecure());
    }

    public function testMissingMetadataIsCreatedForCompatibleAgent(): void
    {
        $this->configure('None');
        $this->header->method('getHttpUserAgent')->willReturn(self::COMPATIBLE_AGENT);

        [, , $result] = $this->plugin->beforeSetPublicCookie(
            $this->createStub(PhpCookieManager::class),
            'form_key',
            'value'
        );

        $this->assertInstanceOf(PublicCookieMetadata::class, $result);
        $this->assertSame('None', $result->getSameSite());
    }

    public function testMissingMetadataStaysNullForIncompatibleAgent(): void
    {
        $this->configure('None');
        $this->header->method('getHttpUserAgent')->willReturn(self::IOS12_AGENT);

        [, , $result] = $this->plugin->beforeSetPublicCookie(
            $this->createStub(PhpCookieManager::class),
            'form_key',
            'value'
        );

        $this->assertNull($result);
    }

    public function testUnaffectedCookieIsUntouched(): void
    {
        $this->configure('None');
        $this->header->method('getHttpUserAgent')->willReturn(self::COMPATIBLE_AGENT);

        [, , $result] = $this->plugin->beforeSetPublicCookie(
            $this->createStub(PhpCookieManager::class),
            'other',
            'value'
        );

        $this->assertNull($result);
    }

    public function testDefaultConfigWithIncompatibleAgentIsUntouched(): void
    {
        $this->configure('Lax');
        $this->header->method('getHttpUserAgent')->willReturn(self::IOS12_AGENT);
        $metadata = new PublicCookieMetadata();

        [, , $result] = $this->plugin->beforeSetPublicCookie(
            $this->createStub(PhpCookieManager::class),
            'form_key',
            'value',
            $metadata
        );

        $this->assertSame('Lax', $result->getSameSite());
        $this->assertNull($result->getSecure());
    }

    public function testEmptyConfigIsUntouched(): void
    {
        $this->configure('');
        $this->header->method('getHttpUserAgent')->willReturn(self::COMPATIBLE_AGENT);

        [, , $result] = $this->plugin->beforeSetPublicCookie(
            $this->createStub(PhpCookieManager::class),
            'form_key',
            'value'
        );

        $this->assertNull($result);
    }
}
