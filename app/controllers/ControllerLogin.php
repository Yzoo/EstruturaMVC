<?php

namespace app\controllers;

use app\database\builder\InsertQuery;


class ControllerLogin extends Base
{
    public function login($request, $response)
    {
        $TemplateData = [
            'titulo' => 'Autenticação'
        ];
        return $this->getTwig()->render(
            $response,
            $this->setView('login'),
            $TemplateData
        )
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function insert($request, $response)
    {
        try {
            $form = $request->getParsedBody();
            #Recupera os dados do nome e converte para uma string.
            $nome = filter_var($form['nome'], FILTER_UNSAFE_RAW);
            $login = filter_var($form['login'], FILTER_UNSAFE_RAW);
            $email = filter_var($form['email'], FILTER_UNSAFE_RAW);
            $senha = password_hash(filter_var($form['senha'], FILTER_UNSAFE_RAW), PASSWORD_DEFAULT);
            $IsSave = InsertQuery::table('usuarios')
                ->save([
                    'nome' => $nome,
                    'login' => $login,
                    'email' => $email,
                    'senha'  => $senha
                ]);
            if ($IsSave != true) {
                $data = [
                    'status' => false,
                    'msg' => 'Restrição: ' . $IsSave,
                    'id' => 0
                ];
                $json = json_encode($data, JSON_UNESCAPED_UNICODE);
                $response->getBody()
                    ->write($json);
                return $response->withStatus(403)
                    ->withHeader('Content-type', 'application/json');
            }
            $data = [
                'status' => true,
                'msg' => 'Registro salvo com sucesso!',
                'id' => 0
            ];
            $json = json_encode($data, JSON_UNESCAPED_UNICODE);
            $response->getBody()
                ->write($json);
            return $response->withStatus(201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withStatus(403)
                ->withHeader('Content-type', 'application/json');
        }
    }
}
