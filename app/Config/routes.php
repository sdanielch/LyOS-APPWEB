<?php
#Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
#Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
Router::connect('/', array('controller' => 'web', 'action' => 'index'));
Router::connect('/logout', array('controller' => 'users', 'action' => 'logout'));
Router::connect('/login', array('controller' => 'users', 'action' => 'login'));
Router::connect('/add', array('controller' => 'users', 'action' => 'add'));
Router::connect('/profile/*', array('controller' => 'users', 'action' => 'profile'));
Router::connect('/finish', array('controller' => 'users', 'action' => 'finish'));
Router::connect('/lang', array('controller' => 'users', 'action' => 'i10n'));
Router::connect('/mpt/*', array('controller' => 'general', 'action' => 'mpt'));
Router::connect('/sw.js', array('controller' => 'general', 'action' => 'sw'));
Router::connect('/prototype.js', array('controller' => 'lovers', 'action' => 'prototype'));
Router::connect('/lovers/backend.php', array('controller' => 'lovers', 'action' => 'backend'));
// ESAS SON LAS RUTAS GENERALES, AHORA EMPEZAMOS CON LAS DE OPENLOVE
//Router::connect('/dashboard', array('controller' => 'Panel', 'action' => 'index'));


// CARGAMOS LAS RUTAS EN EL NUCLEO
CakePlugin::routes();
require CAKE . 'Config' . DS . 'routes.php';
