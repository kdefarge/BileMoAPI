<?php

namespace App\Controller;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Repository\CustumerRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        return $this->render('security/homepage.html.twig');
    }

    /**
     * @Route("/login", name="app_login", methods={"POST"})
     */
    public function login(IriConverterInterface $iriConverter)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json".'
            ], 400);
        }

        return $this->json(null, 204, [
            'Location' => $iriConverter->getIriFromItem($this->getUser())
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/api/tokens", methods={"POST"})
     */
    public function newTokenAction(
        Request $request,
        CustumerRepository $custumerRepository,
        PasswordEncoderInterface $encoder
    ) {
        $custumer = $custumerRepository->findOneBy(['email']);

        if (!$custumer) {
            throw $this->createNotFoundException('No custumer');
        }

        $isValid = $encoder->isPasswordValid(
            $custumer->getPassword(),
            $request->get('password'),
            $custumer->getSalt()
        );

        if (!$isValid) {
            throw new BadCredentialsException();
        }

        return new Response('TOKEN!');
    }
}
