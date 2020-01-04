<?php

From::_('SCSS', $fn = function(string $in, $minify = false) {
    $scss = new ScssPhp\ScssPhp\Compiler;
    $scss->setImportPaths([]); // Disable `@import` rule
    $scss->setFormatter("\\ScssPhp\\ScssPhp\\Formatter\\" . ($minify ? 'Crunched' : 'Expanded'));
    $d = __DIR__ . \DS . '..' . \DS . '..' . \DS . 'state';
    if ($function = (function($f) {
        extract($GLOBALS, \EXTR_SKIP);
        return require $f;
    })($d . \DS . 'function.php')) {
        foreach ((array) $function as $k => $v) {
            $scss->registerFunction($k, $v);
        }
    }
    if ($variable = (function($f) {
        extract($GLOBALS, \EXTR_SKIP);
        return require $f;
    })($d . \DS . 'variable.php')) {
        $scss->setVariables((array) $variable);
    }
    return $scss->compile($in);
});

// Alias(es)
From::_('scss', $fn);
