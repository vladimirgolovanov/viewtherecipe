<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TelegramAuthenticator extends AbstractAuthenticator
{
    private const AUTH_PATH = '/api/auth/telegram';
    private const MAX_AUTH_AGE_SECONDS = 86400;

    public function __construct(
        #[Autowire(env: 'TELEGRAM_BOT_TOKEN')]
        private readonly string $botToken,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return self::AUTH_PATH === $request->getPathInfo();
    }

    public function authenticate(Request $request): Passport
    {
        $params = $request->query->all();

        if (!isset($params['id'], $params['auth_date'], $params['hash'])) {
            throw new AuthenticationException('Missing required Telegram auth parameters.');
        }

        $hash = $params['hash'];
        unset($params['hash']);
        unset($params['redirect']);
        ksort($params);

        $dataCheckString = implode("\n", array_map(
            static fn (string $k, string $v): string => "$k=$v",
            array_keys($params),
            array_values($params),
        ));

        $secretKey = hash('sha256', $this->botToken, true);

        if (!hash_equals(hash_hmac('sha256', $dataCheckString, $secretKey), $hash)) {
            throw new AuthenticationException('Telegram hash verification failed.');
        }

        if (time() - (int) $params['auth_date'] > self::MAX_AUTH_AGE_SECONDS) {
            throw new AuthenticationException('Telegram auth data is outdated.');
        }

        $telegramId = (int) $params['id'];

        return new SelfValidatingPassport(
            new UserBadge((string) $telegramId, fn (string $id): User => $this->loadOrCreateUser((int) $id))
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User $user */
        $user = $token->getUser();

        $redirect = $request->query->get('redirect');
        if ($redirect) {
            return new RedirectResponse($redirect);
        }

        return new JsonResponse(['success' => true]);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['error' => strtr($exception->getMessageKey(), $exception->getMessageData())],
            Response::HTTP_UNAUTHORIZED,
        );
    }

    private function loadOrCreateUser(int $telegramId): User
    {
        $user = $this->userRepository->findOneBy(['telegram_user_id' => $telegramId]);

        if (null === $user) {
            $user = new User();
            $user->setTelegramUserId($telegramId);
            $user->setApiToken(bin2hex(random_bytes(32)));
            $this->em->persist($user);
            $this->em->flush();

            return $user;
        }

        if (null === $user->getApiToken()) {
            $user->setApiToken(bin2hex(random_bytes(32)));
            $this->em->flush();
        }

        return $user;
    }
}
