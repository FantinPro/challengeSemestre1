<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RandomAdController extends AbstractController
{
    public function __construct(
        private AdRepository $adRepository,
    ){}

    public function __invoke(Request $request)
    {
        $ads = $this->adRepository->getAdsFromToday();

        return $this->getRandomAd($ads);
    }

    private function getRandomAd(array $ads)
    {
        $total = 0;
        foreach ($ads as $ad) {
            $total += $ad->getPrice();
        }

        $random = mt_rand(0, $total - 1);
        $current = 0;
        foreach ($ads as $ad) {
            $current += $ad->getPrice();
            if ($random < $current) {
                return $ad;
            }
        }
    }
}
