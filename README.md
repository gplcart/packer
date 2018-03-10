[![Build Status](https://scrutinizer-ci.com/g/gplcart/packer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/packer/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/packer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/packer/?branch=master)

Packer is a [GPL Cart](https://github.com/gplcart/gplcart) module that tries to solve "4D" bin packing/knapsack problem. Useful when you need to know box size/weight to calculate shipping costs. Allows to automatically define the number of boxes needed to fit all the products while adding a new order (saved under `$order['data']['packages']`). Administrators can also check whether a list of products fits into a box.
Based on the [BoxPacker](https://github.com/dvdoug/BoxPacker) library.

**Installation**

This module requires 3-d party library which should be downloaded separately. You have to use [Composer](https://getcomposer.org) to download all the dependencies.

1. From your web root directory: `composer require gplcart/packer`. If the module was downloaded and placed into `system/modules` manually, run `composer update` to make sure that all 3-d party files are presented in the `vendor` directory.
2. Go to `admin/module/list` end enable the module
3. Grant module specific permissions at `admin/user/role`
4. Boxes are managed at `admin/settings/box`