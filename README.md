Custom PDF
=============
Custom PDF overwrites standard PDF layouts for invoices, shipments and creditmemos.
It also makes it possible to use HTML templates for pdf generation

Facts
-----
- version: 1.0.0

Description
-----------
This module will overwrite the default magento PDF functionality for invoices, shipments and creditmemos.
It uses HTML templates to render the PDF.

This allows you to use your magento theme CSS files and enables customisation for Frontend-developers

Requirements
------------
- PHP >= 5.3.0
- Mage_Core
- Mage_Sales

Compatibility
-------------
- Magento >= 1.6

Installation Instructions
-------------------------
* Using Modman
	```bash
	modman clone https://github.com/TimVroom/CustomPdf.git
	```
* Using Composer
	In your composer.json:
	```json
	"require": { 
		"timvroom/custompdf": "dev-master"
    },
	"repositories": [
        {
            "type": "git",
            "url": "git@github.com:TimVroom/CustomPdf.git"
        }
    ],
	```

Uninstallation
--------------


Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/TimVroom/custompdf/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Tim Vroom (NicheCommerce)

License
-------
[GNU General Public License, version 3 (GPLv3)](http://opensource.org/licenses/gpl-3.0)

Copyright
---------
(c) 2013-2014 NicheCommerce
