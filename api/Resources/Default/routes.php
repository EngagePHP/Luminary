<?php

$router->get('/', function () use ($app) {
    return $app->version();
});
