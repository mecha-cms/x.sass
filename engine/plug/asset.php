<?php

namespace x\sass {
    function files(string $path): array {}
    \Asset::_('*.sass', function ($value, $key) {});
}

namespace x\scss {
    function files(string $path): array {
        if (!\is_file($path)) {
            return [];
        }
        $out = [$path];
        $pattern = '/@import\s+(?:([\'"])([^\'"\s]+)\1|url\(([\'"]?)([^\n]+)\1\))/';
        foreach (\stream($path) as $v) {
            if (false !== \strpos($v, '@import') && \preg_match_all($pattern, $v, $m)) {
                foreach ($m[2] as $vv) {
                    if (
                        false !== \strpos($vv, '://') ||
                        0 === \strpos($vv, '//') ||
                        '.css' === \substr($vv, -4)
                    ) {
                        // Ignore external file(s) and native CSS file(s)
                        continue;
                    }
                    $vv = \dirname($path) . \D . \strtr($vv, '/', \D);
                    if (\is_file($vv) || \is_file($vv .= '.scss')) {
                        $out = \array_merge($out, \x\scss\files($vv)); // Recurse…
                    }
                }
            }
        }
        return $out;
    }
    \Asset::_('*.scss', function ($value, $key) {
        $data = $value[2];
        $link = $value['link'];
        $path = $value['path'];
        $stack = $value['stack'];
        $x = false !== \strpos($link, '://') || 0 === \strpos($link, '//');
        if (!$path && !$x) {
            return '<!-- ' . $key . ' -->';
        }
        $file = \strtr($path . \P, [
            \D . 'scss' . \D => \D . 'css' . \D,
            \D . \basename($path) . \P => \D . \basename($path, '.scss') . '.css',
            \P => ""
        ]);
        $f = \substr($file, 0, -4) . '.min.css';
        if (!\is_dir($folder = \dirname($file))) {
            \mkdir($folder, 0777, true);
        }
        $current = 0;
        foreach (\x\scss\files($path) as $v) {
            $v = \filemtime($v);
            $current < $v && ($current = $v);
        }
        if (!\is_file($file) || $current > \filemtime($file) || !\is_file($f) || $current > \filemtime($f)) {
            $scss = new \ScssPhp\ScssPhp\Compiler;
            $scss->setImportPaths([\dirname($path)]);
            $folder = __DIR__ . \D . '..' . \D . '..' . \D . 'state';
            if ($function = (static function ($f) {
                \extract($GLOBALS, \EXTR_SKIP);
                return require $f;
            })($folder . \D . 'function.php')) {
                foreach ((array) $function as $k => $v) {
                    $scss->registerFunction($k, $v);
                }
            }
            if ($variable = (static function ($f) {
                \extract($GLOBALS, \EXTR_SKIP);
                return require $f;
            })($folder . \D . 'variable.php')) {
                $scss->setVariables((array) $variable);
            }
            $content = \file_get_contents($path);
            $scss->setFormatter("\\ScssPhp\\ScssPhp\\Formatter\\Expanded");
            \file_put_contents($file, $scss->compile($content));
            \chmod($file, 0777);
            $scss->setFormatter("\\ScssPhp\\ScssPhp\\Formatter\\Crunched");
            \file_put_contents($f, $scss->compile($content));
            \chmod($f, 0777);
        }
        return \Asset::set(\defined("\\TEST") && \TEST ? $file : $f, $stack, $data);
    });
}