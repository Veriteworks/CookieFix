<?php
namespace Veriteworks\CookieFix\Test\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Veriteworks\CookieFix\Validator\SameSite;

/**
 * Class SameSiteTest
 * @package Veriteworks\CookieFix\Test\Unit\Validator
 */
class SameSiteTest extends TestCase
{
    /**
     * @var SameSite
     */
    private $validator;

    /**
     * setup test
     */
    public function setUp()
    {
        $this->validator = new SameSite();
    }

    /**
     * @param $useragent
     * @param $expected
     *
     *  @dataProvider iphoneAgentProvider
     */
    public function testIphone($useragent, $expected)
    {
        $result = $this->validator->shouldSendSameSiteNone($useragent);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param $useragent
     * @param $expected
     *
     *  @dataProvider ipadAgentProvider
     */
    public function testIpad($useragent, $expected)
    {
        $result = $this->validator->shouldSendSameSiteNone($useragent);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param $useragent
     * @param $expected
     *
     *  @dataProvider chromeAgentProvider
     */
    public function testChrome($useragent, $expected)
    {
        $result = $this->validator->shouldSendSameSiteNone($useragent);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param $useragent
     * @param $expected
     *
     *  @dataProvider chromiumAgentProvider
     */
    public function testChromium($useragent, $expected)
    {
        $result = $this->validator->shouldSendSameSiteNone($useragent);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param $useragent
     * @param $expected
     *
     *  @dataProvider safariAgentProvider
     */
    public function testSafari($useragent, $expected)
    {
        $result = $this->validator->shouldSendSameSiteNone($useragent);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param $useragent
     * @param $expected
     *
     *  @dataProvider ucbrowserAgentProvider
     */
    public function testUcBrowser($useragent, $expected)
    {
        $result = $this->validator->shouldSendSameSiteNone($useragent);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function iphoneAgentProvider()
    {
        return [
            [
                'Mozilla/5.0 (iPhone; CPU iPhone OS 11_2_5 like Mac OS X) AppleWebKit/604.5.6 (KHTML, like Gecko) Version/11.0 Mobile/15D60 Safari/604.1',
                false
            ],
            [
                'Mozilla/5.0 (iPhone; CPU iPhone OS 12_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1',
                false
            ],
            [
                'Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.4 Mobile/15E148 Safari/604.1',
                true
            ],
        ];
    }

    /**
     * @return array
     */
    public function ipadAgentProvider()
    {
        return [
            [
                'Mozilla/5.0 (iPad; CPU OS 11_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/11.0 Mobile/15E148 Safari/604.1',
                false
            ],
            [
                'Mozilla/5.0 (iPad; CPU OS 12_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1 Mobile/15E148 Safari/604.1',
                false
            ],
            [
                'Mozilla/5.0 (iPad; CPU OS 13_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.5 Mobile/15E148 Safari/604.1',
                true
            ],
        ];
    }

    /**
     * @return array
     */
    public function chromeAgentProvider()
    {
        return [
            [
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36',
                true
            ],
            [
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36',
                false
            ],
            [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36 OPR/54.0.2952.64',
                false
            ],
        ];
    }

    /**
     * @return array
     */
    public function chromiumAgentProvider()
    {
        return [
            [
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chromium/80.0.3729.157 Chrome/80.0.3729.157 Safari/537.36',
                true
            ],
            [
                'Mozilla/5.0 (X11; Linux armv7l) AppleWebKit/537.36 (KHTML, like Gecko) Raspbian Chromium/74.0.3729.157 Chrome/74.0.3729.157 Safari/537.36',
                true
            ]

        ];
    }

    /**
     * @return array
     */
    public function safariAgentProvider()
    {
        return [
            [
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.1 Safari/605.1.15',
                false
            ],
            [
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.5 Safari/605.1.15',
                true
            ],
        ];
    }

    /**
     * @return array
     */
    public function ucbrowserAgentProvider()
    {
        return [
            [
                'Mozilla/5.0(Linux;U;Android 5.1.1;zh-CN;OPPO A33 Build/LMY47V) AppleWebKit/537.36(KHTML,like Gecko) Version/4.0 Chrome/40.0.2214.89 UCBrowser/11.7.0.953 Mobile Safari/537.36',
                false
            ],
            [
                'Mozilla/5.0(Linux;U;Android 5.1.1;zh-CN;OPPO A33 Build/LMY47V) AppleWebKit/537.36(KHTML,like Gecko) Version/4.0 Chrome/40.0.2214.89 UCBrowser/12.13.2.953 Mobile Safari/537.36',
                true
            ],
            [
                'Mozilla/5.0(Linux;U;Android 5.1.1;zh-CN;OPPO A33 Build/LMY47V) AppleWebKit/537.36(KHTML,like Gecko) Version/4.0 Chrome/40.0.2214.89 UCBrowser/12.13.1.953 Mobile Safari/537.36',
                false
            ],
            [
                'Mozilla/5.0(Linux;U;Android 5.1.1;zh-CN;OPPO A33 Build/LMY47V) AppleWebKit/537.36(KHTML,like Gecko) Version/4.0 Chrome/40.0.2214.89 UCBrowser/12.12.0.953 Mobile Safari/537.36',
                false
            ],
        ];
    }

}
