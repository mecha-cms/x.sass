<?php namespace fn\scss;

function files(string $path): array {
    if (!\is_file($path)) {
        return [false, []];
    }
    $out = [$path];
    $content = \file_get_contents($path);
    $r = '#@import\s+(?:([\'"])([^"\'\s]+)\1|url\(([\'"]?)([^\n]+)\1\))#';
    if (\strpos($content, '@import') !== false && \preg_match_all($r, $content, $m)) {
        foreach ($m[2] as $v) {
            // Ignore external file(s) and native CSS file(s)
            if (
                \strpos($v, '://') !== false ||
                \strpos($v, '//') === 0 ||
                \substr($v, -4) === '.css'
            ) {
                continue;
            }
            $v = \dirname($path) . DS . \strtr($v, '/', DS);
            if (\is_file($v) || \is_file($v .= '.scss')) {
                $out = \concat($out, files($v)[1]); // Recurse…
            }
        }
    }
    return [$content, $out];
}

\Asset::_('.scss', function($value, $key, $data) {
    extract($value, \EXTR_SKIP);
    $state = \Extend::state('asset');
    if (isset($path)) {
        $scss = new \scssc;
        $scss->setFormatter('scss_formatter_compressed');
        $scss->setImportPaths([dirname($path)]);
        if ($function = \Extend::state('scss:function')) {
            foreach ((array) $function as $k => $v) {
                $scss->registerFunction($k, $v);
            }
        }
        if ($variable = \Extend::state('scss:variable')) {
            $scss->setVariables((array) $variable);
        }
        $result = str_replace([
            DS . 'scss' . DS,
            DS . \basename($path) . X,
            X
        ], [
            DS . 'css' . DS,
            DS . \Path::N($path) . '.min.css',
            ""
        ], $path . X);
        $t = 0;
        $files = files($path);
        foreach ($files[1] as $v) {
            $v = \filemtime($v);
            $t < $v && ($t = $v);
        }
        if (!\file_exists($result) || $t > \filemtime($result)) {
            $css = $scss->compile($files[0]);
            // Optimize where possible
            if (\Extend::exist('minify')) {
                $css = \Minify::css($css);
            }
            \File::put($css)->saveTo($result);
        }
        return \HTML::unite('link', false, \extend($data, [
            'href' => \candy($state['url'], [\To::URL($result), $t ?: $_SERVER['REQUEST_TIME']]),
            'rel' => 'stylesheet'
        ]));
    }
    return '<!-- ' . $key . ' -->';
});