<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegistrationTest extends WebTestCase
{
    /** @test */
    public function registration_is_successful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'register');

        $form = $crawler->filter("form[name=registration_form]")->form([
            "registration_form[email]" => "user+new@email.com",
            "registration_form[plainPassword]" => "password",
            "registration_form[agreeTerms]" => true
        ]);
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();
        $this->assertRouteSame('home');
    }

    /**
     * @test
     * @dataProvider provideInvalidForm
     */
    public function form_is_invalid(array $formData, string $errorMessage): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'register');

        $form = $crawler->filter("form[name=registration_form]")->form($formData);
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();
        $this->assertRouteSame('app_register');

        $this->assertSelectorTextContains(".alert.alert-danger", $errorMessage);
    }

    public function provideInvalidForm(): iterable
    {
        yield [
            [
                "registration_form[email]" => "",
                "registration_form[plainPassword]" => "password",
                "registration_form[agreeTerms]" => true
            ],
            "The 'email' field cannot be empty."
        ];
    }
}