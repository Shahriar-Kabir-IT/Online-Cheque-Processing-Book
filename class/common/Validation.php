<?php
/**
 * Input validation and sanitization class
 */
class Validation
{
    public static function sanitizeString($input, $allowHtml = false): string
    {
        if (!is_string($input)) {
            return '';
        }
        $input = trim($input);
        if ($allowHtml) {
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }
        return strip_tags($input);
    }

    public static function validateInt($input, $min = null, $max = null)
    {
        $value = filter_var($input, FILTER_VALIDATE_INT);
        if ($value === false) {
            return false;
        }
        if ($min !== null && $value < $min) {
            return false;
        }
        if ($max !== null && $value > $max) {
            return false;
        }
        return $value;
    }

    public static function validateFloat($input, $min = null, $max = null)
    {
        $value = filter_var($input, FILTER_VALIDATE_FLOAT);
        if ($value === false) {
            return false;
        }
        if ($min !== null && $value < $min) {
            return false;
        }
        if ($max !== null && $value > $max) {
            return false;
        }
        return $value;
    }

    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validateDate($date, $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function getPost($key, $default = '', $type = 'string')
    {
        if (!isset($_POST[$key])) {
            return $default;
        }
        $value = $_POST[$key];
        switch ($type) {
            case 'int':
                $validated = self::validateInt($value);
                return $validated !== false ? $validated : $default;
            case 'float':
                $validated = self::validateFloat($value);
                return $validated !== false ? $validated : $default;
            case 'email':
                $validated = self::validateEmail($value);
                return $validated !== false ? $validated : $default;
            case 'date':
                return self::validateDate($value) ? $value : $default;
            default:
                return self::sanitizeString($value);
        }
    }

    public static function getGet($key, $default = '', $type = 'string')
    {
        if (!isset($_GET[$key])) {
            return $default;
        }
        $value = $_GET[$key];
        switch ($type) {
            case 'int':
                $validated = self::validateInt($value);
                return $validated !== false ? $validated : $default;
            case 'float':
                $validated = self::validateFloat($value);
                return $validated !== false ? $validated : $default;
            case 'email':
                $validated = self::validateEmail($value);
                return $validated !== false ? $validated : $default;
            case 'date':
                return self::validateDate($value) ? $value : $default;
            default:
                return self::sanitizeString($value);
        }
    }
}
