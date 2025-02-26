<?php
require_once '../vendor/autoload.php';

session_start();

$client = new Google\Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // Get profile info
    $oauth = new Google\Service\Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    // Store user information in session or database
    $_SESSION['user'] = $userInfo;
    header('Location: index.php');
    exit;
}