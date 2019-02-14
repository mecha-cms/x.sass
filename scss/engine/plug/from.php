<?php

From::_('SCSS', $fn = function(string $in, $minify = false) {
    $scss = new scssc;
    $scss->setImportPaths([]); // Disable `@import` rule
    $scss->setFormatter('scss_formatter' . ($minify ? '_compressed' : ""));
    if ($function = Extend::state('scss:function')) {
        foreach ((array) $function as $k => $v) {
            $scss->registerFunction($k, $v);
        }
    }
    if ($variable = Extend::state('scss:variable')) {
        $scss->setVariables((array) $variable);
    }
    $out = $scss->compile($in);
    return $minify && Extend::exist('minify') ? Minify::css($out) : $out;
});

// Alias(es)
From::_('scss', $fn);