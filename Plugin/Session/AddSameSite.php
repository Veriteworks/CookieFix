<?php
namespace Veriteworks\CookieFix\Plugin\Session;

use Magento\Framework\HTTP\Header;
use Magento\Framework\Session\Config;
use Veriteworks\CookieFix\Validator\SameSite;

class AddSameSite
{
    /**
     * @var SameSite
     */
    private $validator;
    /**
     * @var Header
     */
    private $header;

    /**
     * AddSameSite constructor.
     * @param SameSite $validator
     * @param Header $header
     */
    public function __construct(
        SameSite $validator,
        Header $header
    ) {
        $this->validator = $validator;
        $this->header = $header;
    }

    /**
     * @param \Magento\Framework\Session\Config $subject
     * @param $result
     * @param string $cookiePath
     * @param string|null $default
     */
    public function afterSetCookiePath(
        Config $subject,
        $result,
        $cookiePath,
        $default = null
    ) {
        $version = PHP_VERSION_ID;
        $agent = $this->header->getHttpUserAgent();
        $sameSite = $this->validator->shouldSendSameSiteNone($agent);

        if (!$sameSite) {
            return $result;
        }

        $subject->setOption('session.cookie_secure', 1);

        if ($version >= 70300) {
            $subject->setOption('session.cookie_samesite', 'None');
        } else {
            $path = $subject->getCookiePath();
            if (!preg_match('/SameSite/', $path)) {
                $path .= '; SameSite=None';
                $subject->setOption('session.cookie_path', $path);
            }
        }

        return $result;
    }
}
