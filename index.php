<?php

namespace x\sass {
    function asset() {
        // Output is not required in this case, just trigger the asset(s)
        if (\class_exists("\\Asset")) {
            \Asset::join('*.sass');
            \Asset::join('*.scss');
        }
    }
    // Make sure to run this hook before `head`
    \Hook::set('content', __NAMESPACE__ . "\\asset", -1);
}

namespace {
    require __DIR__ . \D . 'engine' . \D . 'vendor' . \D . 'autoload.php';
    if (\defined("\\TEST") && 'x.sass' === \TEST) {
        \Asset::set(__DIR__ . \D . 'test.scss', 20);
    }
}