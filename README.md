# Translation Management for OpenMage/Magento

The "Translation Management" module simplifies translation tasks in OpenMage by providing an efficient way to create and update missing and existing translations from various modules. This module offers a user-friendly grid/edit dashboard in the Admin panel, allowing you to manage translations with ease. Additionally, it can detect and capture missing translations when browsing the frontend.

## Features

- **Admin Translation Grid**: Access a dedicated grid in the Admin panel to create and update translations.
- **Missing Translation Detection**: Automatically capture missing translations while navigating the frontend.
- **CSV File Management**: Update and manage CSV translation files for different languages.
- **Dynamic Module Configuration**: If a module's CSV file is missing, the module guides you through the process of creating and updating it in the config.xml file.

## Installation

### Composer

```json
{
    "minimum-stability": "dev",
    "require": {
        "m-michalis/om-translator": "0.1.*"
    }
}
```

## Usage

1. Enable `Configuration > Advanced > Developer > Translate [InternetCode] > Enabled` for specified scope/store_view.
2. Navigate frontend pages and/or backend processes like sending emails.
3. Check out `System > Translation Management > Missing Translations` and adjust.
4. `System > Translation Management > Manage Translations` for existing translations.


## Compatibility
- OpenMage 20.0.x
- Magento 1.9.x

## Roadmap & TODOs
- Auto-disable "catching" after certain time for improved performance

## License
This module is released under the GPL-3.0 License.
