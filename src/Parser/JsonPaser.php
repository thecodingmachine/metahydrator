<?php
/**
 * Created by PhpStorm.
 * User: loic
 * Date: 06/11/18
 * Time: 11:03
 */

namespace MetaHydrator\Parser;


use MetaHydrator\Exception\ParsingException;

class JsonPaser extends AbstractParser
{
    /**
     * @param $value
     * @return mixed
     * @throws ParsingException
     */
    public function jsonValid($value) {
        $result = json_decode($value);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = '';
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occured.';
                break;
        }

        if ($error !== '') {
            throw new ParsingException($error);
        }

        return $result;
    }

    /**
     * @param $rawValue
     * @return mixed|null
     * @throws \MetaHydrator\Exception\ParsingException
     */
    public function parse($rawValue)
    {
        if ($rawValue === null || $rawValue === '') {
            return null;
        } else {
            $value = filter_var($rawValue, FILTER_CALLBACK, array('options' => 'jsonValid'));
            if ($value === null) {
                $this->throw();
            }
            return $value;
        }
    }
}
