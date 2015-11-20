<?php

namespace IFP\Adverts\Sales;

trait QueryStringTrait
{
    private function buildQueryString($params)
    {
        return implode('&', array_map(function ($key, $value) {
            return $this->buildQueryStringParam($key, $value);
        }, array_keys($params), $params));
    }

    private function buildQueryStringParam($key, $value)
    {
        if (! is_array($value)) {
            return $key . '=' . $value;
        }

        if ($this->isAssoc($value)) {
            return $this->buildAssocParam($key, $value);
        }

        return $this->buildListParam($key, $value);
    }

    private function buildAssocParam($key, $value)
    {
        $key_value_pairs = [];

        foreach ($value as $subkey => $subvalue) {
            $key_value_pairs["{$key}[{$subkey}]"] = $subvalue;
        }

        return $this->buildQueryString($key_value_pairs);
    }

    private function buildListParam($key, $value)
    {
        return $key . '=' . implode(',', $value);
    }

    private function isAssoc($array)
    {
        $keys = array_keys($array);

        foreach ($keys as $key) {
            if (is_string($key)) {
                return true;
            }
        }

        return false;
    }
}
