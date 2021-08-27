<?php namespace x\scss;

function files(string $path): array {
    if (!\is_file($path)) {
        return [];
    }
    $out = [$path];
    foreach (\stream($path) as $v) {
        $r = '/@import\s+(?:([\'"])([^\'"\s]+)\1|url\(([\'"]?)([^\n]+)\1\))/';
        if (false !== \strpos($v, '@import') && \preg_match_all($r, $v, $m)) {
            foreach ($m[2] as $vv) {
                if (
                    false !== \strpos($vv, '://') ||
                    0 === \strpos($vv, '//') ||
                    '.css' === \substr($vv, -4)
                ) {
                    // Ignore external file(s) and native CSS file(s)
                    continue;
                }
                $vv = \dirname($path) . \DS . \strtr($vv, '/', \DS);
                if (\is_file($vv) || \is_file($vv .= '.scss')) {
                    $out = \array_merge($out, files($vv)); // Recurse…
                }
            }
        }
    }
    return $out;
}

\Asset::_('.scss', function($value, $key) {
    $data = $value[2];
    $path = $value['path'];
    $stack = $value['stack'];
    $url = $value['url'];
    $x = false !== \strpos($url, '://') || 0 === \strpos($url, '//');
    if (!$path && !$x) {
        return '<!-- ' . $key . ' -->';
    }
    $f = \str_replace([
        \DS . 'scss' . \DS,
        \DS . \basename($path) . \P,
        \P
    ], [
        \DS . 'css' . \DS,
        \DS . \basename($path, '.scss') . '.css',
        ""
    ], $path . \P);
    $ff = \substr($f, 0, -4) . '.min.css';
    if (!\is_dir($d = \dirname($f))) {
        \mkdir($d, 0777, true);
    }
    $update = 0;
    foreach (files($path) as $v) {
        $v = \filemtime($v);
        $update < $v && ($update = $v);
    }
    if (
        !\is_file($f) || $update > \filemtime($f) ||
        !\is_file($ff) || $update > \filemtime($ff)
    ) {
        $scss = new \ScssPhp\ScssPhp\Compiler;
        $scss->setImportPaths([\dirname($path)]);
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
        $content = \file_get_contents($path);
        $scss->setFormatter("\\ScssPhp\\ScssPhp\\Formatter\\Expanded");
        $css = $scss->compile($content);
        \file_put_contents($f, $css);
        \chmod($f, 0777);
        $scss->setFormatter("\\ScssPhp\\ScssPhp\\Formatter\\Crunched");
        $css = $scss->compile($content);
        \file_put_contents($ff, $css);
        \chmod($f, 0777);
    }
    return static::set($ff, $stack, $data);
});