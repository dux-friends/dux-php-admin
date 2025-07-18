<?php

namespace App\System\Web;

use Core\App;
use Core\Route\Attribute\Route;
use Core\Route\Attribute\RouteGroup;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[RouteGroup(app: 'web', route: '/manage')]
class Manage
{
    #[Route(methods: 'GET', route: '')]
    public function location(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $response->withStatus(302)->withHeader('Location', '/manage/');
    }

    #[Route(methods: 'GET', route: '/')]
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = json_decode(file_get_contents(public_path('/static/web/.vite/manifest.json')) ?: '', true);
        $vite = App::config('use')->get('vite', []);
        $lang = App::config('use')->get('app.lang', 'en-US');

        $assign = [
            "title" => App::config('use')->get('app.name'),
            "lang" => $lang,
            'vite' => [
                'dev' => (bool)$vite['dev'],
                'port' => $vite['port'] ?: 5173,
            ],
            'manifest' => [
                'js' => $data['main.ts']['file'],
                'css' => $data['style.css']['file'],
            ],
            'config' => [
                'defaultManage' => 'admin',
                'theme' => [
                    'logo' => null,
                    'darkLogo' => null,
                    'banner' => null,
                    'darkBanner' => null,
                ],
                'copyright' => App::config('use')->get('app.copyright'),
                'manage' => [
                    [
                        'name' => 'admin',
                        'title' => App::config('use')->get('app.name'),
                        'description' => '',
                        'routePrefix' => '/admin',
                        'apiBasePath' => '/admin',
                        'apiRoutePath' => '/router',
                        'userMenus' => [
                            [
                                'key' => 'setting',
                                "label" => "个人资料",
                                "icon" => "i-tabler:settings",
                                "path" => "system/profile",
                            ]
                        ],
                    ],
                ],
            ],
        ];

        return sendTpl($response, dirname(__DIR__) . "/Views/manage.latte", $assign);
    }
}
