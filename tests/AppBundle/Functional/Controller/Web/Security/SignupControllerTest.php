<?php

namespace Tests\AppBundle\Functional\Controller\Web\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SignupControllerTest extends WebTestCase
{
    public function testSignup()
    {
        $username = 'signup.' . time() . '@test.com';
        $password = 'test';

        $client = static::createClient();
        $crawler = $client->request('GET', '/signup');
        $buttonCrawlerNode = $crawler->selectButton('user_signup_form[signup_submit]');
        $form = $buttonCrawlerNode->form();
        $data = array('user_signup_form[email]' => $username, 'user_signup_form[plainPassword]' => $password);
        $client->submit($form, $data);
        $client->followRedirect();


        // проверим что юзер залогинился и это именно тот юзер, которого мы указали
        $crawler = $client->request('GET', '/settings');

        $pageTitle = $crawler->filter('h1')->text();
        $this->assertEquals('Настройки', $pageTitle);

        $emailFromSettings = $crawler->filter('#change_email_email')->attr("value");
        $this->assertEquals($username, $emailFromSettings);
    }


    public function testSignupExistingEmail()
    {
        $username = 'user@test.com';
        $password = 'test';

        $client = static::createClient();
        $crawler = $client->request('GET', '/signup');
        $buttonCrawlerNode = $crawler->selectButton('user_signup_form[signup_submit]');
        $form = $buttonCrawlerNode->form();
        $data = array('user_signup_form[email]' => $username, 'user_signup_form[plainPassword]' => $password);
        $client->submit($form, $data);
        try {
            $client->followRedirect();
        } catch (\LogicException $e) {
            // если регистрация прошла успешно - редирект будет, иначе нет.
            // а когда редиректа нет, то выбрасывается данное исключение
        }

        // проверим что юзер не залогинен
        $client->request('GET', '/settings');
        $redirectLocation = $client->getResponse()->headers->get("Location");
        $this->assertEquals("/login", $redirectLocation);
    }
}