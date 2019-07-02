<?php

// Resize
Router::prefix('resize', ['_namePrefix' => 'resize:'], function (RouteBuilder $routes) {
    $routes->connect(
        '/image/:folder/:dimension/t:img.:img_ext',
        ['controller' => 'Resize', 'action' => 'imageResize'],
        [
            'pass' => ['folder', 'img', 'dimension', 'img_ext'],
            '_name' => 'imgResize'
        ]
    );
    $routes->connect(
        '/image/proportional/:folder/:dimension/t:img.:img_ext',
        ['controller' => 'Resize', 'action' => 'imageResizeProportional'],
        [
            'pass' => ['folder', 'img', 'dimension', 'img_ext'],
            '_name' => 'imgProportional'
        ]
    );
});
