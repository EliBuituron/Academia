<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

session_start();

if (PHP_SAPI == 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) return false;
}

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

$mongoconn = new \MongoDB\Client("mongodb://localhost");
$userService = new \Tuiter\Services\UserService($mongoconn->tuiter->users);
$postService = new \Tuiter\Services\PostService($mongoconn->tuiter->posts);
$likeService = new \Tuiter\Services\LikeService($mongoconn->tuiter->likes);
$followService = new \Tuiter\Services\FollowService($mongoconn->tuiter->follows, $userService);
$loginService = new \Tuiter\Services\LoginService($userService);


$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, array $args) use ($twig) {
    $template = $twig->load('index.html');
    $response->getBody()->write(
        $template->render()
    );
    return $response;
});

$app->post('/', function (Request $request, Response $response, array $args) use ($loginService) {
    $loginService->login($_POST['username'],$_POST['password']);
    if($_SESSION['login'] == true){
        $response = $response->withStatus(302);
        return $response->withHeader("Location", "/feed");
    }else{
        $response = $response->withStatus(302);
        return $response->withHeader("Location", "/");
    }
});

$app->get('/register', function (Request $request, Response $response, array $args) use ($twig) {
    $template = $twig->load('register.html');
    $response->getBody()->write(
        $template->render()
    );
    return $response;
});

$app->post('/register', function (Request $request, Response $response, array $args) use ($userService) {
    $result = $userService->register($_POST['username'],$_POST['username'],$_POST['password']);
    if($result == true){
        $response = $response->withStatus(302);
        return $response->withHeader("Location", "/");
    }else{
        $response = $response->withStatus(302);
        return $response->withHeader("Location", "/register");
    }
});

$app->get('/logout', function (Request $request, Response $response, array $args) use ($loginService){
    $loginService->logout();
    $response = $response->withStatus(302);
    return $response->withHeader("Location", "/");
});

$app->get('/feed', function (Request $request, Response $response, array $args) use ($twig, $followService, $postService, $userService) {
    $template = $twig->load('feed.html');
    $followed = $followService->getFollowed($_SESSION['user']);
    $postObjects = array();
    $posts = array();

    foreach($followed as $user){
        $postObjects[] = $postService->getAllPosts($user);
        foreach ($postObjects as $post){
            $posts[] = array(
                $post['owner'],
                $post['content']
            );
        }
    }

    $response->getBody()->write(
        $template->render()
    );
    return $response;
});

$app->get('/user/me', function (Request $request, Response $response, array $args) use ($twig){

    $response = $response->withStatus(302);
    return $response->withHeader("Location", "/");
});

$app->run();