<?php

namespace x {
    function sass() {
        // Output is not required in this case, just trigger the asset(s)
        \class_exists("\\Asset") && \Asset::join('.sass');
    }
    function scss() {
        // Output is not required in this case, just trigger the asset(s)
        \class_exists("\\Asset") && \Asset::join('.scss');
    }
    // Make sure to run this hook before `asset:head`
    \Hook::set('content', __NAMESPACE__ . "\\sass", -1);
    \Hook::set('content', __NAMESPACE__ . "\\scss", -1);
}

// Create/update the `css.data` file by monitoring the change/existence of the `scss.data` file
namespace x\art {
    function sass($content) {
        if (!$this->exist) {
            return $content;
        }
        $change = 0;
        $folder = \dirname($path = $this->path) . \D . \pathinfo($path, \PATHINFO_FILENAME);
        if (\is_file($from = $folder . \D . 'sass.data')) {
            $change = \filemtime($from);
        } else {
            $change = \filemtime($path);
        }
        if (!\is_file($to = $folder . \D . 'css.data') || $change > \filemtime($to)) {
            if ($out = $this->sass) {
                $out = \From::SASS($out);
                if (!\is_dir($folder)) {
                    \mkdir($folder, 0775, true);
                }
                \file_put_contents($to, $out);
                return $out;
            }
        }
        return $content;
    }
    function scss($content) {
        if (!$this->exist) {
            return $content;
        }
        $current = 0;
        $folder = \dirname($path = $this->path) . \D . \pathinfo($path, \PATHINFO_FILENAME);
        if (\is_file($from = $folder . \D . 'scss.data')) {
            $current = \filemtime($from);
        } else {
            $current = \filemtime($path);
        }
        if (!\is_file($to = $folder . \D . 'css.data') || $current > \filemtime($to)) {
            if ($out = $this->scss) {
                $out = \From::SCSS($out);
                if (!\is_dir($folder)) {
                    \mkdir($folder, 0775, true);
                }
                \file_put_contents($to, $out);
                return $out;
            }
        }
        return $content;
    }
    \Hook::set('page.css', __NAMESPACE__ . "\\sass", 0);
    \Hook::set('page.css', __NAMESPACE__ . "\\scss", 0);
}