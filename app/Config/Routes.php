<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// In app/Config/Routes.php

$routes->get('/', 'Home::index'); // Default controller
$routes->set404Override(); // 404 override
// $routes->translateURIDashes(false); // Translate URI dashes

// Static Pages
$routes->get('about', 'Home::aboutUs');
$routes->get('contact', 'Home::contactUs');
$routes->get('projects', 'Home::projects');
$routes->get('service/hardware', 'Home::hardware');
$routes->get('service/embedded', 'Home::embedded');
$routes->get('service/software', 'Home::software');

// Admin Pages
$routes->group('admin', function($routes) {
    $routes->get('/', 'ManageLogin::index');
    $routes->get('faqs', 'ManageHome::faqs');
    $routes->get('comments', 'ManageHome::comments');
    $routes->get('articles', 'ManageHome::articles');
    $routes->get('portfolios', 'ManageHome::portfolios');
    $routes->get('portfolio/edit/(:num)', 'ManageHome::portfolio_edit/$1');
    $routes->get('logout', 'ManageLogin::logout');
});

// APIs
$routes->group('api', function($routes) {
    $routes->post('question/get', 'ManageHome::getQuestion');
    $routes->post('question/add', 'ManageHome::addQuestion');
    $routes->post('question/update', 'ManageHome::updateQuestion');
    $routes->post('question/delete', 'ManageHome::deleteQuestion');

    $routes->post('comment/get', 'ManageHome::getComment');
    $routes->post('comment/add', 'ManageHome::addComment');
    $routes->post('comment/update', 'ManageHome::updateComment');
    $routes->post('comment/delete', 'ManageHome::deleteComment');

    $routes->post('article/add', 'ManageHome::addArticle');
    $routes->post('article/get', 'ManageHome::getArticle');
    $routes->post('article/update', 'ManageHome::updateArticle');
    $routes->post('article/delete', 'ManageHome::deleteArticle');

    $routes->post('portfolio/delete', 'ManageHome::deletePortfolio');
    $routes->post('portfolio/update', 'ManageHome::updatePortfolio');

    $routes->post('slide/add', 'ManageHome::addSlide');
    $routes->post('slide/get', 'ManageHome::getSlide');
    $routes->post('slide/update', 'ManageHome::updateSlide');
    $routes->post('slide/delete', 'ManageHome::deleteSlide');

    $routes->post('portfolio/description/add', 'ManageHome::addPortfolioDescription');
    $routes->post('portfolio/description/get', 'ManageHome::getPortfolioDescription');
    $routes->post('portfolio/description/update', 'ManageHome::updatePortfolioDescription');
    $routes->post('portfolio/description/delete', 'ManageHome::deletePortfolioDescription');

    $routes->post('sendEmail', 'Home::sendEmail');
});

