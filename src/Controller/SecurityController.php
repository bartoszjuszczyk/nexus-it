<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_cms_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/user/2fa/qr_code', name: 'app_2fa_qr_code', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getTotpQrCode(TotpAuthenticatorInterface $totpAuthenticator): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->isTotpAuthenticationEnabled()) {
            return new JsonResponse();
        }
        $code = $totpAuthenticator->generateSecret();
        $user->setTotpAuthenticationSecret($code);
        $writer = new PngWriter();
        $qrCodeContent = $totpAuthenticator->getQRContent($user);
        $qrCode = new QrCode($qrCodeContent, size: 400);
        $result = $writer->write($qrCode);

        return new JsonResponse([
            'qrCodeUri' => $result->getDataUri(),
            'authCode' => $user->getTotpAuthenticationSecret(),
        ]);
    }

    #[Route(path: '/user/2fa/confirm', name: 'app_2fa_confirm', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function confirm2fa(
        Request $request,
        TotpAuthenticatorInterface $totpAuthenticator,
        EntityManagerInterface $entityManager,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->isTotpAuthenticationEnabled()) {
            $this->addFlash('warning', 'Two-Factor Authentication already enabled.');

            return $this->redirectToRoute('app_user_edit_mine');
        }
        $otpCode = $request->request->get('otp_code');
        $authCode = $request->request->get('auth_code');
        $user->setTotpAuthenticationSecret($authCode);

        $result = $totpAuthenticator->checkCode($user, $otpCode);

        if ($result) {
            $user->setTotpAuthenticationSecret($authCode);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Two-Factor Authentication successfully enabled.');

            return $this->redirectToRoute('app_user_edit_mine');
        }

        $this->addFlash('error', 'Provided 6-digits code is invalid.');

        return $this->redirectToRoute('app_user_edit_mine');
    }

    #[Route(path: '/user/2fa/disable', name: 'app_2fa_disable', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function disable2fa(
        EntityManagerInterface $entityManager,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->isTotpAuthenticationEnabled()) {
            $this->addFlash('warning', 'Two-Factor Authentication is not enabled.');

            return $this->redirectToRoute('app_user_edit_mine');
        }
        try {
            $user->setTotpAuthenticationSecret(null);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Two-Factor authentication disabled successfully.');
        } catch (\Exception $exception) {
            $this->addFlash('error', 'There was an error while trying to disable two-factor authentication.');
        }

        return $this->redirectToRoute('app_user_edit_mine');
    }
}
