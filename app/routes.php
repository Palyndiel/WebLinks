<?php

use Symfony\Component\HttpFoundation\Request;
use WebLinks\Domain\Link;
use WebLinks\Form\Type\LinkType;

// Home page
$app->match('/', function (Request $request) use ($app) {
    //$links = $app['dao.link']->findAll();
    $linkFormView = null;
    if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
        // A user is fully authenticated : he can add links
        $link = new Link();
        $user = $app['user'];
        $link->setAuthor($user);
        $linkForm = $app['form.factory']->create(new LinkType(), $link);
        $linkForm->handleRequest($request);
        if ($linkForm->isSubmitted() && $linkForm->isValid()) {
            $app['dao.link']->save($link);
            $app['session']->getFlashBag()->add('success', 'Your link was succesfully added.');
        }
        $linkFormView = $linkForm->createView();
    }
    $links = $app['dao.link']->findAll();
    return $app['twig']->render('index.html.twig', array('links' => $links, 'linkForm' => $linkFormView));
})->bind('home');

// Login form
$app->get('/login', function(Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login');

// Admin home page
$app->get('/admin', function() use ($app) {
    $links = $app['dao.link']->findAll();
    $users = $app['dao.user']->findAll();
    return $app['twig']->render('admin.html.twig', array(
        'links' => $links,
        'users' => $users));
})->bind('admin');