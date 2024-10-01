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
                     #Recupera os dados do nome e converte para uma string.
            $form = $request->getParsedBody();
            $email = filter_var($form['email'], FILTER_UNSAFE_RAW);
            $senha = password_hash(filter_var($form['senha'], FILTER_UNSAFE_RAW), PASSWORD_DEFAULT);
            $nome = filter_var($form['nome'], FILTER_UNSAFE_RAW);
            $login = filter_var($form['login'], FILTER_UNSAFE_RAW);
            



            $IsSave = InsertQuery::table('usuarios')
                ->save([

                    'email'     =>   $email,
                    'senha'     =>   $senha,
                    'nome'      =>   $nome,
                    'login'     =>   $login,
                    

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
            return $response
                ->withStatus(201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {

            var_dump($e->getMessage());
            throw new \PDOException("ERRO ERRO ERRO ERRO ERRO ERRO ERRO" . $e->getMessage());
        }
    }

    public function autenticacao($request, $response)
    {
        try {
            $form = $request->getParsedBody();
            $login = filter_var($form['login'], FILTER_UNSAFE_RAW);
            $senha = filter_var($form['senha'], FILTER_UNSAFE_RAW);

            $usuario = SelectQuery::select()
                ->from('usuarios')
                ->where('login', '=', $login)
                ->fetch();

            if (!$usuario || !password_verify($senha, $usuario['senha'])) {
                $data = [
                    'status' => false,
                    'msg' => 'Restrição: Não foi possível Logar.',
                    'id' => 0
                ];
                $json = json_encode($data, JSON_UNESCAPED_UNICODE);
                $response->getBody()
                    ->write($json);
                return $response->withStatus(403)
                    ->withHeader('Content-type', 'application/json');
                {
                    $templateData = [
                        'titulo' => 'Usuario'
                    ];
                    return $this->getTwig()->render(
                        $response,
                        $this->setView('usuario'),
                        $templateData
                    )
                        ->withHeader('Content-Type', 'text/html')
                        ->withStatus(200);
                }
            }

            // Criação da sessão do usuário
            $_SESSION['login'] = [
                'logado' => true,
                'nome' => $_SESSION['nome'],
                'login' => $_SESSION['login']
            ];

            return $response->withHeader('Location', '/home')->withStatus(302);
        } catch (\Exception $e) {
            return $this->respondWithJson($response, [
                'status' => false,
                'msg' => 'Erro ao processar a autenticação.'
            ], 500);
        }
    }

    public function logout($request, $response)
    {
        session_destroy(); // Destroi a sessão
        return $this->respondWithJson($response, [
            'status' => true,
            'msg' => 'Logout realizado com sucesso!'
        ], 200);
    }

    public function verificaAutenticacaoUsuario()
    {
        return isset($_SESSION['login']);
    }

    private function respondWithJson($response, $data, $status)
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        return $response->withStatus($status)
            ->withHeader('Content-Type', 'application/json')
            ->write($json);
    }
}
