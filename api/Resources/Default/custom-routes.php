<?php

$router->get('/health-check', function () use ($app) {
    return $app->version();
});
