<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CouponRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CouponController extends AbstractController
{
    public function __construct(private readonly CouponRepository $couponRepository)
    {
    }

    #[Route('/coupons/verify', name: 'app_coupons_verify', methods: ['POST'])]
    public function index(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $code = $content['code'] ?? null;

        if ($code === null) {
            return new JsonResponse(['success' => false, 'message' => "Le code du coupon est obligatoire"]);
        }

        $coupon = $this->couponRepository->findOneBy(['code' => $code]);

        if ($coupon === null) {
            return new JsonResponse(['success' => false, 'message' => "Le coupon n'existe pas"]);
        }

        if ($coupon->getExpirationDate() !== null && $coupon->getExpirationDate()->getTimestamp() < (new DateTime())->getTimestamp()) {
            return new JsonResponse(['success' => false, 'message' => "Le coupon a expiré depuis le " . $coupon->getExpirationDate()->format('d-m-Y')]);
        }

        return new JsonResponse(['success' => true, 'percent' => $coupon->getPercent()]);
    }
}