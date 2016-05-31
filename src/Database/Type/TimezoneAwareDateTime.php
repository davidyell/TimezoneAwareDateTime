<?php
namespace App\Database\Type;

use Cake\Database\Type\DateTimeType;

/**
 * Datetime type converter.
 *
 * Use to convert datetime instances to strings & back.
 */
class TimezoneAwareDateTimeType extends DateTimeType
{
    const SOURCE = 'Europe/London';
    const TARGET = 'UTC';

    /**
     * Returns the base type name that this class is inheriting.
     * This is useful when extending base type for adding extra functionality
     * but still want the rest of the framework to use the same assumptions it would
     * do about the base type it inherits from.
     *
     * @return string
     */
    public function getBaseType()
    {
        return 'datetime';
    }

    /**
     * Convert request data into a datetime object.
     *
     * @param mixed $value Request data
     * @return \Cake\I18n\Time
     */
    public function marshal($value)
    {
        date_default_timezone_set(self::SOURCE);
        if ($value instanceof \DateTime) {
            $value->setTimezone(new \DateTimeZone(self::TARGET));
            return $value;
        }

        $class = parent::useMutable();

        try {
            $compare = $date = false;
            if ($value === '' || $value === null || $value === false || $value === true) {
                return null;
            } elseif (is_numeric($value)) {
                $date = new $class('@' . $value);
                $date->timezone(new \DateTimeZone(self::TARGET));
            } elseif (is_string($value) && $this->_useLocaleParser) {
                return $this->_parseValue($value);
            } elseif (is_string($value)) {
                $date = new $class($value, new \DateTimeZone(self::TARGET));
                $compare = true;
            }
            if ($compare && $date && $date->format($this->_format) !== $value) {
                return $value;
            }
            if ($date) {
                return $date;
            }
        } catch (\Exception $e) {
            return $value;
        }

        if (is_array($value) && implode('', $value) === '') {
            return null;
        }
        $value += ['hour' => 0, 'minute' => 0, 'second' => 0];

        $format = '';
        if (isset($value['year'], $value['month'], $value['day']) &&
            (is_numeric($value['year']) && is_numeric($value['month']) && is_numeric($value['day']))
        ) {
            $format .= sprintf('%d-%02d-%02d', $value['year'], $value['month'], $value['day']);
        }

        if (isset($value['meridian'])) {
            $value['hour'] = strtolower($value['meridian']) === 'am' ? $value['hour'] : $value['hour'] + 12;
        }
        $format .= sprintf(
            '%s%02d:%02d:%02d',
            empty($format) ? '' : ' ',
            $value['hour'],
            $value['minute'],
            $value['second']
        );

        $date = new $class($format, new \DateTimeZone(self::SOURCE));
        $date->timezone(self::TARGET);
        return $date;

    }

    /**
     * Converts a string into a DateTime object after parseing it using the locale
     * aware parser with the specified format.
     *
     * @param string $value The value to parse and convert to an object.
     * @return \Cake\I18n\Time|null
     */
    protected function _parseValue($value)
    {
        date_default_timezone_set(self::SOURCE);
        $value = parent::_parseValue($value);
        date_default_timezone_set(self::TARGET);
        $value->timezone(self::TARGET);
        return $value;
    }
}
