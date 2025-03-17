<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repo\UserRepo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Slim\Exception\HttpUnauthorizedException;
use Valitron\Validator;

class AuthEndPoint
{
    public function __construct(
        private UserRepo $UserR,
        private Validator $validator
    ) {
        $this->validator->mapFieldsRules([
            "phone" => ['required', ['length', 11]],
            'password' => ['required']
        ]);
    }

    public function __invoke(Request $req, Response $res): Response
    {

        $data = $req->getParsedBody();

        $this->validator = $this->validator->withData($data);

        if (!$this->validator->validate()) {
            $res->getBody()->write(json_encode($this->validator->errors()));
            return $res->withStatus(422);
        }

        $phone = $data['phone'];
        $password = $data['password'];

        $user = $this->UserR->getByPhone($phone);

        if ($user === false || !password_verify($password, $user["password"])) {
            throw new HttpUnauthorizedException($req, message: "phone or password is incorrect!");
        }

        $payload = [
            "iat" => time(),
            "exp" => time() + (60 * 60 * 24),
            "user_id" => $user["id"],
        ];

        $token = JWT::encode($payload, "8e4baba57ab5ebbdde59ff06c34df2fd260daf6d7fc1b8c0e4066ed0074a8606", "HS256");

        $body = json_encode(['token' => $token]);
        $res->getBody()->write($body);
        return $res;
    }
}
