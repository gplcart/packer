[![Build Status](https://scrutinizer-ci.com/g/gplcart/packer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/packer/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/packer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/packer/?branch=master)

Packer is a [GPL Cart](https://github.com/gplcart/gplcart) module that tries to solve "4D" bin packing/knapsack problem. Useful when you need to know box size/weight to calculate shipping costs. Allows to automatically define the number of boxes needed to fit all the products while adding a new order (saved under `$order['data']['packages']`). Administrators can also check whether a list of products fits into a box.
Based on the [BoxPacker](https://github.com/dvdoug/BoxPacker) library.

**Installation**

1. Download and extract to `system/modules` manually or using composer `composer require gplcart/packer`. IMPORTANT: If you downloaded the module manually, be sure that the name of extracted module folder doesn't contain a branch/version suffix, e.g `-master`. Rename if needed.
2. Go to `admin/module/list` end enable the module
3. Grant module specific permissions at `admin/user/role`
4. Boxes are managed at `admin/settings/box`