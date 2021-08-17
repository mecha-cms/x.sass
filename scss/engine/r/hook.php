<?php

// Automatically generate `css.data` file from `scss.data` file
namespace x\art {
    function scss($content) {
        if (!$path = $this->path) {
            return $content;
        }
        $update = 0;
        $d = \Path::F($path);
        if (\is_file($f = $d . \DS . 'scss.data')) {
            $update = \filemtime($f);
        } else {
            $update = \filemtime($path);
        }
        $ff = $d . \DS . 'css.data';
        if (!\is_file($ff) || $update > \filemtime($ff)) {
            if ($out = $this->scss) {
                $out = \From::SCSS($out);
                if (!\is_dir($d)) {
                    \mkdir($d, 0775, true);
                }
                \file_put_contents($ff, $out);
                return $out;
            }
        }
        return $content;
    }
    \Hook::set('page.css', __NAMESPACE__ . "\\scss", 0);
}

namespace x {
    function scss() {
        // No output needed, just trigger the asset(s)
        \Asset::join('.scss');
    }
    // Make sure to run before `asset:head`
    \Hook::set('content', __NAMESPACE__ . "\\scss", -1);
}
