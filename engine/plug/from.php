<?php

From::_('SASS', $sass = static function (?string $content, $tidy = true): ?string {});

From::_('SCSS', $scss = static function (string $content, $tidy = true): ?string {
    $scss = new ScssPhp\ScssPhp\Compiler;
    $scss->setImportPaths([]); // Disable `@import` rule
    $scss->setFormatter("\\ScssPhp\\ScssPhp\\Formatter\\" . ($tidy ? 'Expanded' : 'Crunched'));
    $folder = __DIR__ . D . '..' . D . '..' . D . 'state';
    if ($function = (static function ($f) {
        extract($GLOBALS, EXTR_SKIP);
        return require $f;
    })($folder . D . 'function.php')) {
        foreach ((array) $function as $k => $v) {
            $scss->registerFunction($k, $v);
        }
    }
    if ($variable = (static function ($f) {
        extract($GLOBALS, EXTR_SKIP);
        return require $f;
    })($folder . D . 'variable.php')) {
        $scss->setVariables((array) $variable);
    }
    $content = $scss->compile($content ?? "");
    return "" !== $content ? $content : null;
});

// Alias(es)
From::_('sass', $sass);
From::_('scss', $scss);