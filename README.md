# wp-live-content

[![Code Style](https://github.com/yardinternet/wp-live-content/actions/workflows/format-php.yml/badge.svg?no-cache)](https://github.com/yardinternet/wp-live-content/actions/workflows/format-php.yml)
[![PHPStan](https://github.com/yardinternet/wp-live-content/actions/workflows/phpstan.yml/badge.svg?no-cache)](https://github.com/yardinternet/wp-live-content/actions/workflows/phpstan.yml)
[![Tests](https://github.com/yardinternet/wp-live-content/actions/workflows/run-tests.yml/badge.svg?no-cache)](https://github.com/yardinternet/wp-live-content/actions/workflows/run-tests.yml)
[![Code Coverage Badge](https://github.com/yardinternet/wp-live-content/blob/badges/coverage.svg)](https://github.com/yardinternet/wp-live-content/actions/workflows/badges.yml)
[![Lines of Code Badge](https://github.com/yardinternet/wp-live-content/blob/badges/lines-of-code.svg)](https://github.com/yardinternet/wp-live-content/actions/workflows/badges.yml)

## Requirements

- [Sage](https://github.com/roots/sage) >= 10.0
- [Acorn](https://github.com/roots/acorn) >= 4.0

## Installation

To install this package using Composer, follow these steps:

1. Add the following to the `repositories` section of your `composer.json`:

    ```json
    {
      "type": "vcs",
      "url": "git@github.com:yardinternet/wp-live-content.git"
    }
    ```

2. Install this package with Composer:

    ```sh
    composer require yard/wp-live-content
    ```

3. Run the Acorn WP-CLI command to discover this package:

    ```shell
    wp acorn package:discover
    ```

You can publish the config file with:

```shell
wp acorn vendor:publish --provider="Yard\LiveContent\LiveContentServiceProvider"
```

## Usage

From a Blade template:

```blade
<x-yard-live-content post-id="{{ $postId }}" />
```
