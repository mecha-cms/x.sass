<?php

namespace x {
    function sass() {
        // Output is not required in this case, just trigger the asset(s)
        \class_exists("\\Asset") && \Asset::join('*.sass');
    }
    function scss() {
        // Output is not required in this case, just trigger the asset(s)
        \class_exists("\\Asset") && \Asset::join('*.scss');
    }
    // Make sure to run this hook before `asset:head`
    \Hook::set('content', __NAMESPACE__ . "\\sass", -1);
    \Hook::set('content', __NAMESPACE__ . "\\scss", -1);
}

namespace {
    require __DIR__ . \D . 'engine' . \D . 'vendor' . \D . 'autoload.php';
    if (\defined("\\TEST") && 'x.sass' === \TEST) {
        \Asset::set(__DIR__ . \D . 'test.scss', 20);
    }
}