<?php

From::_('SCSS', $fn = function(string $in, $minify = false) {
    $scss = new scssc;
    $scss->setImportPaths([]); // Disable `@import` rule
    $scss->setFormatter('scss_formatter' . ($minify ? '_compressed' : ""));
    if ($function = extend('scss:function')) {
        foreach ((array) $function as $k => $v) {
            $scss->registerFunction($k, $v);
        }
    }
    if ($variable = extend('scss:variable')) {
        $scss->setVariables((array) $variable);
    }
    $out = $scss->compile($in);
    return $minify && extend('minify') !== null ? Minify::CSS($out) : $out;
});

// Alias(es)
From::_('scss', $fn);