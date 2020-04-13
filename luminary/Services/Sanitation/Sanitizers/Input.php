<?php

namespace Luminary\Services\Sanitation\Sanitizers;

class Input
{
    /**
     * Sanitize an input
     *
     * @param mixed $input
     * @param string $type
     * @return mixed
     */
    public static function sanitize($input, string $type)
    {
        $instance = new static;

        //for a rule containing more than one type, like arr|int
        $arrAttribute = explode("|", $type);
        $type = $arrAttribute[0] ?: $type;
        $arrType = isset($arrAttribute[1]) ? $arrAttribute[1] : null;

        return method_exists($instance, $type) ? $instance->{$type}($input, $arrType) : null;
    }

     /**
      * Sanitize an array
      * $input is array values
      * $type is type of array values
      *
      * @param array $input
      * @param string $type
      * @return mixed
      */
    public function arr(array $input, string $type)
    {
        $instance = new static;

        foreach ($input as $key => $value) {
            $input[$key] = method_exists($instance, $type) ? $instance->{$type}($value) : null;
        }

        return $input;
    }

     /**
      * return null if field is empty
      * otherwise sanitize as string
      *
      * @param $input
      * @return null|string
      */
    public function date($input)
    {
        if (empty($input)) {
            return null;
        }

        return $this->string($input);
    }

     /**
     * Sanitize a string
     *
     * @param mixed $input
     * @return string | null
     */
    public function string($input)
    {
        if (empty($input)) {
            return null;
        }

        return filter_var(
            $input,
            FILTER_SANITIZE_STRIPPED,
            FILTER_FLAG_NO_ENCODE_QUOTES
        );
    }

    /**
     * Filter a float
     * Remove all characters except digits, +-
     *
     * @param mixed $input
     * @return mixed
     */
    public function float($input)
    {
        $filter = filter_var(
            $input,
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );

        return floatval($filter);
    }

    /**
     * Filter a float
     * Remove all characters except digits, +-
     *
     * @param mixed $input
     * @return mixed
     */
    public function floatOrNull($input)
    {
        if ($input === "") {
            return null;
        }

        return $this->float($input);
    }

    /**
     * Sanitize an integer
     *
     * @param mixed $input
     * @return int | null
     */
    public function integer($input)
    {
        $filter = filter_var(
            $input,
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );

        return is_numeric($filter) ? (int) $filter : null;
    }

    /**
     * Alias for integer
     *
     * @param $input
     * @return int|null
     */
    public function int($input)
    {
        return $this->integer($input);
    }

    /**
     * Sanitize a telephone number
     *
     * @param $input
     * @return mixed
     */
    public function phone($input)
    {
        $phone = preg_replace('/[^0-9]/', '', $input);
        $length = strlen($phone);

        switch ($length) {
            case 7:
                return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
                break;
            case 9:
                break;
            case 10:
                return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
                break;
            case 11:
                $phone = ltrim($phone, '1');
                return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
                break;
            default:
                return $phone;
                break;
        }
    }

    /**
     * Sanitize an email
     * Remove all characters except letters, digits and !#$%&'*+-/=?^_`{|}~@.[].
     *
     * @param mixed $input
     * @return mixed
     */
    public function email($input)
    {
        $filtered = filter_var(
            $input,
            FILTER_SANITIZE_EMAIL
        );

        return strtolower($filtered);
    }

    /**
     * Sanitize a url string
     * Remove all characters except letters, digits and $-_.+!*'(),{}|\\^~[]`<>#%";/?:@&=.
     *
     * @param mixed $input
     * @return mixed
     */
    public function url($input)
    {
        return filter_var(
            $input,
            FILTER_SANITIZE_URL
        );
    }

    /**
     * Sanitize html input
     * HTML-escape '"<>& and characters with ASCII value less than 32
     *
     * @param mixed $input
     * @return mixed
     */
    public function html($input)
    {
        return filter_var(
            $input,
            FILTER_SANITIZE_SPECIAL_CHARS
        );
    }

    /**
     * Sanitize Boolean
     *
     * @param mixed $input
     * @return boolean
     */
    public function boolean($input)
    {
        return filter_var(
            $input,
            FILTER_VALIDATE_BOOLEAN
        );
    }

     /**
      * wrapper function for boolean
      *
      * @param $input
      * @return bool
      */
    public function bool($input)
    {
        return $this->boolean($input);
    }

    /**
     * Sanitize raw input
     * Strip or encode special characters
     *
     * @param mixed $input
     * @return mixed
     */
    public function raw($input)
    {
        return filter_var(
            $input,
            FILTER_UNSAFE_RAW,
            FILTER_FLAG_STRIP_LOW
        );
    }
}
