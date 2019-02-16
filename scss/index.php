<?php

require __DIR__ . DS . 'lot' . DS . 'worker' . DS . '@scss' . DS . 'scss.inc.php';

require __DIR__ . DS . 'engine' . DS . 'plug' . DS . 'asset.php';
require __DIR__ . DS . 'engine' . DS . 'plug' . DS . 'from.php';

Hook::set('asset:head', function($content) {
    return $content . Hook::fire('asset.scss', [Asset::scss()], null, Asset::class); // Append
});

// Add `scss` to the allowed file extension(s)
File::$config['x'][] = 'scss';