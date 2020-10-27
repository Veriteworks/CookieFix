<?php
namespace Veriteworks\CookieFix\Model\Config\Source;

class SameSite
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Lax','label' => __('Lax')],
            ['value' => 'Strict','label' => __('Strict')],
            ['value' => 'None','label' => __('None')]
        ];
    }
}
