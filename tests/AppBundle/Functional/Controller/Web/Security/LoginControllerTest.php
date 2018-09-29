<?php

namespace Tests\AppBundle\Functional\Controller\Web\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $username = 'user@test.com';
        $password = 'test';

        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $buttonCrawlerNode = $crawler->selectButton('login_submit');
        $form = $buttonCrawlerNode->form();
        $data = array('_username' => $username, '_password' => $password);
        $client->submit($form, $data);
        $client->followRedirect();


        // проверим что юзер залогинился и это именно тот юзер, которого мы указали
        $crawler = $client->request('GET', '/settings');

        $pageTitle = $crawler->filter('h1')->text();
        $this->assertEquals('Настройки', $pageTitle);

        $emailFromSettings = $crawler->filter('#change_email_email')->attr("value");
        $this->assertEquals($username, $emailFromSettings);
    }


    public function testBadPasswordLogin()
    {
        $username = 'user@test.com';
        $password = 'bad_password';

        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $buttonCrawlerNode = $crawler->selectButton('login_submit');
        $form = $buttonCrawlerNode->form();
        $data = array('_username' => $username, '_password' => $password);
        $client->submit($form, $data);


        // проверим что юзер не залогинен
        $client->request('GET', '/settings');
        $redirectLocation = $client->getResponse()->headers->get("Location");
        $this->assertEquals("/login", $redirectLocation);
    }
}