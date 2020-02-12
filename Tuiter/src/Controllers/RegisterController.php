<?php

namespace Tuiter\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RegisterController implements \Tuiter\Interfaces\Controller {

    public function config($app) {

        $app->get('/register', function (Request $request, Response $response, array $args) {

            $template = $request->getAttribute("twig")->load('register.html');
            $response->getBody()->write($template->render());
            return $response;
        });

        $app->post('/register', function (Request $request, Response $response, array $args) {

            $result = $request->getAttribute("userService")->register(
                $_POST['username'],$_POST['name'],$_POST['password']
            );

            $response = $response->withStatus(302);
            
            if($result){
                return $response->withHeader("Location", "/");
            }
            return $response->withHeader("Location", "/register");
        });
        
    }
}