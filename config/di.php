<?php

return [
    'request' => function() {
        return \Laminas\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
    },

    'response' => new \Laminas\Diactoros\Response(),
    'emitter' => new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter(),

    \Twig\Environment::class => function() {
        $loader = new Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
        
        $twig = new \Twig\Environment($loader);
        $twig->addGlobal('session', $_SESSION);
        $twig->addExtension(new \Knlv\Slim\Views\TwigMessages(new \Slim\Flash\Messages()));
        return $twig;
    }
];