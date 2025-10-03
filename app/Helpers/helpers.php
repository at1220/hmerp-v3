<?php
if (!function_exists('safeEnumLabel')) {
    function safeEnumLabel(string $class, ?string $value): string
    {
        return $value ? $class::from($value)->getLabel() : '';
    }
}
