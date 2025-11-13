<?php

use App\Helpers\ResponseBuilder;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        \App\Console\Commands\SendNewJobNotificationCommand::class,
        \App\Console\Commands\SendNewsNotificationCommand::class,
        \App\Console\Commands\ArchiveExpiredJobs::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.admin' => \App\Http\Middleware\AdminAuth::class,
            'auth.alumni' => \App\Http\Middleware\AlumniAuth::class,
            'auth.company' => \App\Http\Middleware\CompanyAuth::class,
            'verified.company' => \App\Http\Middleware\EnsureCompanyIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ResponseBuilder::fail()
                    ->httpCode(Response::HTTP_NOT_FOUND)
                    ->message('Data Tidak Ditemukan')
                    ->build();
            }
        });
    })->create();
