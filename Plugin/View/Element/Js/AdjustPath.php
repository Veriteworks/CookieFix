<?php
namespace Veriteworks\CookieFix\Plugin\View\Element\Js;

use Magento\Framework\View\Element\Js\Cookie;

class AdjustPath
{

    /**
     * @param Cookie $subject
     * @param $result
     */
    public function afterGetPath(Cookie $subject, $result)
    {
        if (preg_match('/SameSite/', $result)) {
            $str = explode(';', $result);
            $result = $str[0];
        }
        return $result;
    }
}
