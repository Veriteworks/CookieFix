<?php
namespace Veriteworks\CookieFix\Plugin\View\Element\Js;

use Magento\Framework\View\Element\Js\Cookie;

class AdjustPath
{

    /**
     * @param \Magento\Framework\View\Element\Js\Cookie $subject
     * @param $result
     */
    public function afterGetPath(\Magento\Framework\View\Element\Js\Cookie $subject, $result)
    {
        if (preg_match('/SameSite/', $result)) {
            $str = explode(';', $result);
            $result = $str[0];
        }
        return $result;
    }
}
