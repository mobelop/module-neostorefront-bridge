# NeoStorefront Bridge

This extension allows real-time catalog updates for [gatsby-source-magento2](https://github.com/mobelop/gatsby-source-magento2) meaning
your Gatsby.js store pages will automatically refresh whenever you update your catalog.

It is part of the [NeoStorefront](https://www.neostorefront.com/) project which is a **headless PWA storefront for Magento**, which is optimized for the cloud deployments (Vercel, Netlify).

## Installation
 - Copy the module files into `app/code/Mobelop/Bridge/`
 - Enable the module by running `php bin/magento module:enable Mobelop_Bridge`
 - Apply database updates by running `php bin/magento setup:upgrade`
 - Flush the cache by running `php bin/magento cache:flush`
