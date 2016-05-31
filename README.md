# TimezoneAwareDateTime
A CakePHP 3 data type class for dealing with marshalling datetimes into various timezones.

Based on work by [jose_zap](https://github.com/lorenzo)

## Why?
Have you got your CakePHP 3 application setup storing your datetime fields as UTC, but you need to display them to your users in a different timezone? This class aims to tackle the problem by doing the conversion at the marshalling stage.

## Requirements
* PHP 5.5.9+
* CakePHP 3.2+
 
## Installation
TBC. 

Currently copy the class into your `src/Database/Type` folder, and then add it to the type map with `Type::map('datetime', 'App\Database\Type\TimezoneAwareDateTimeType');` in your `bootstrap.php`.

## License
See [LICENSE](LICENSE)
