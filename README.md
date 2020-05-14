[![Build Status](https://travis-ci.com/voku/Php-Readme-Helper.svg?branch=master)](https://travis-ci.com/voku/Php-Readme-Helper)
[![Coverage Status](https://coveralls.io/repos/github/voku/Php-Readme-Helper/badge.svg?branch=master)](https://coveralls.io/github/voku/Php-Readme-Helper?branch=master)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/2005467672aa44abbf2ba57fbad80af1)](https://www.codacy.com/manual/voku/Php-Readme-Helper?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=voku/Php-Readme-Helper&amp;utm_campaign=Badge_Grade)
[![Latest Stable Version](https://poser.pugx.org/voku/Php-Readme-Helper/v/stable)](https://packagist.org/packages/voku/php-readme-helper) 
[![Total Downloads](https://poser.pugx.org/voku/php-readme-helper/downloads)](https://packagist.org/packages/voku/php-readme-helper) 
[![License](https://poser.pugx.org/voku/php-readme-helper/license)](https://packagist.org/packages/voku/php-readme-helper)
[![Donate to this project using Paypal](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://www.paypal.me/moelleken)
[![Donate to this project using Patreon](https://img.shields.io/badge/patreon-donate-yellow.svg)](https://www.patreon.com/voku)

# 📖 PHP README Helper

Helper to auto-generate your README...

### Usage

```php
$readmeText = (new \voku\PhpReadmeHelper\GenerateApi())->generate(
    __DIR__ . '/../src/MyClass.php',
    __DIR__ . '/docs/base.md'
);


file_put_contents(__DIR__ . '/../README.md', $readmeText);
```


### Support

For support and donations please visit [Github](https://github.com/voku/simple_html_dom/) | [Issues](https://github.com/voku/simple_html_dom/issues) | [PayPal](https://paypal.me/moelleken) | [Patreon](https://www.patreon.com/voku).

For status updates and release announcements please visit [Releases](https://github.com/voku/simple_html_dom/releases) | [Twitter](https://twitter.com/suckup_de) | [Patreon](https://www.patreon.com/voku/posts).

For professional support please contact [me](https://about.me/voku).

### Thanks

- Thanks to [GitHub](https://github.com) (Microsoft) for hosting the code and a good infrastructure including Issues-Managment, etc.
- Thanks to [IntelliJ](https://www.jetbrains.com) as they make the best IDEs for PHP and they gave me an open source license for PhpStorm!
- Thanks to [Travis CI](https://travis-ci.com/) for being the most awesome, easiest continous integration tool out there!
- Thanks to [StyleCI](https://styleci.io/) for the simple but powerfull code style check.
- Thanks to [PHPStan](https://github.com/phpstan/phpstan) && [Psalm](https://github.com/vimeo/psalm) for really great Static analysis tools and for discover bugs in the code!
