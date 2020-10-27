# Extension for Cookie SameSite attribute
This extension is adjusting Cookie SameSite attribute issue since Chrome 80.

**NOTICE: this extension is experimental.**

# Features
Add SameSite attribute into Magento session cookie. For 3DS payment, payment gateways tend to send request via POST to Magento based website. However without SameSite=None for session cookie, Magento cannot continue any PHP session state.
This extension set SameSite=None and make customers place order via 3DS payment or other ID integrations. 

# How to install
 + composer require veriteworks/cookiefix
 + bin/magento module:enable Veriteworks_CookieFix
 + bin/magento setup:upgrade
 
# Configuration

After 3.0.0-beta1, you can change SameSite cookie configuration from admin panel. Go to "Stores > configuration > web" and open cookie section. You can see "SameSite" field.
By default, this extension sets SameSite configuration value to limited cookies. If you hope to update the list, please update web/cookie/affected_keys configuration value. 

# Support

If you have any issues about this extension, open an issue on Github.
Technical support question is same. 

# Contribution

Any contributions are highly appreciated. Please send me a pull request.

# License

[Open Software License 3.0](http://opensource.org/licenses/osl-3.0.php)

# Copyright

(c) 2020 Veriteworks Inc.