<?php

namespace app\controllers;

use app\database\builder\InsertQuery;
use app\database\builder\SelectQuery;


class ControllerLogin extends Base
{
    public function login($request, $response)
    {
        $templateData = [
            'titulo' => 'Autenticação'
        ];
        return $this->getTwig()->render(
            $response,
            $this->setView('login'),
            $templateData
        )
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }

    public function insert($request, $response)
    {
        try {
            // Recupera os dados do formulário
            $form = $request->getParsedBody();
            $email = filter_var($form['email'], FILTER_UNSAFE_RAW);
            $senha = password_hash(filter_var($form['senha'], FILTER_UNSAFE_RAW), PASSWORD_DEFAULT);
            $nome = filter_var($form['nome'], FILTER_UNSAFE_RAW);
            $login = filter_var($form['login'], FILTER_UNSAFE_RAW);

            // Tenta salvar os dados do usuário
            $isSave = InsertQuery::table('usuarios')->save([
                'email' => $email,
                'senha' => $senha,
                'nome' => $nome,
                'login' => $login,
            ]);

            // Verifica se a operação de salvamento foi bem-sucedida
            if (!$isSave) {
                return $this->respondWithJson($response, false, 'Restrição: ' . $isSave, 403);
            }

            return $this->respondWithJson($response, true, 'Registro salvo com sucesso!', 201);
        } catch (\Exception $e) {
            return $this->respondWithJson($response, false, 'Erro: ' . $e->getMessage(), 500);
        }
    }

    public function autenticacao($request, $response)
    {
        try {
            // Obtendo o login e a senha do formulário
            $form = $request->getParsedBody();
            $login = filter_var($form['login'] ?? '', FILTER_UNSAFE_RAW);
            $senha = filter_var($form['senha'] ?? '', FILTER_UNSAFE_RAW);
            var_dump($form);

            // Verifique se o login e a senha estão preenchidos
            if (empty($login) && empty($senha)) {
                return $this->respondWithJson($response, false, 'Login e senha são obrigatórios!', 400);
            }

            $user = SelectQuery::select()
                ->from('usuarios')
                ->where('login', '=', $login)
                ->fetch();

            // Checagem do usuário
            if (!$user) {
                return $this->respondWithJson($response, false, 'Usuário não encontrado!', 403);
            }

            // Checagem de senha
            if (!password_verify($senha, $user['senha'])) {
                return $this->respondWithJson($response, false, 'Senha incorreta!', 403);
            }

            // Criação da sessão do usuário
            $_SESSION['usuario'] = [
                'logado' => true,
                'nome' => $user['nome']
            ];

            return $this->respondWithJson($response, true, 'Usuário logado!', 200);
        } catch (\Exception $e) {
            return $this->respondWithJson($response, false, 'Erro: ' . $e->getMessage(), 500);
        }
    }

    private function respondWithJson($response, $status, $message, $httpStatus)
    {
        $data = [
            'status' => $status,
            'msg' => $message,
        ];
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($json);
        return $response->withStatus($httpStatus)
            ->withHeader('Content-type', 'application/json');
    }
}
