<?php

namespace PAC\Includes;

class PAC_Plugin
{
    public static function init() : void
    {
        if (is_admin()) {
            new Admin(PAC_PLUGIN_TEMPLATE_PATH . 'admin/', 10);
        }
    }
}