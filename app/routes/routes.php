<?php

use app\controllers\ControllerCliente;
use app\controllers\ControllerDisciplina;
use app\controllers\ControllerHome;
use app\controllers\ControllerLogin;
use app\middleware\Middleware;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', ControllerHome::class . ':home')->add(Middleware::route());
$app->get('/login', ControllerLogin::class . ':login')->add(Middleware::route());
$app->post('/cadastro', ControllerLogin::class . ':insert');
$app->post('/usuario', ControllerLogin::class . ':autenticacao');
$app->post('/sessao', ControllerLogin::class . ':verificaAutenticacaoUsuario');



$app->group('/cliente', function (RouteCollectorProxy $group) {
    $group->get('/cadastro', ControllerCliente::class . ':cadastro');
});
$app->group('/disciplina', function (RouteCollectorProxy $group) {
    $group->get('/lista', ControllerDisciplina::class . ':lista');
    $group->get('/cadastro', ControllerDisciplina::class . ':cadastro');
    $group->get('/alterar/{id}', ControllerDisciplina::class . ':alterar');
    $group->post('/delete/{id}', ControllerDisciplina::class . ':delete');
    $group->post('/insert', ControllerDisciplina::class . ':insert');
});