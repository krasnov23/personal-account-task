<?php

namespace App\Controller;

use App\Repository\UserAccountRepository;
use App\Service\UserAccountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonalAccountController extends AbstractController
{
    public function __construct(private UserAccountRepository $userAccountRepository,
                                private UserAccountService $userAccountService)
    {
    }

    #[Route('/')]
    public function myServices(Request $request): Response
    {
        $user = $this->userAccountRepository->findAllUserServices(1);

        if ('POST' === $request->getMethod()) {
            $addService = $this->userAccountService->addAndShowUserServices($request,$user);
            $this->addFlash($addService[0],$addService[1]);
            $this->redirectToRoute($addService[2]);
        }

        return $this->render('personal-account/my-services.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/transactions')]
    public function myTransactions(Request $request): Response
    {
        $currentUser = $this->userAccountRepository->findUserTransactions(1);

        if ('POST' === $request->getMethod()) {
            if (3 === count($request->request->all())) {
                return $this->render('personal-account/my-transactions.html.twig', [
                    'user' => $currentUser,
                    'transactions' => $this->userAccountService->sortTransactionsByDateOrName($request,$currentUser->getId())
                ]);
            } else {
                $this->userAccountService->addMoneyToUserBalance($request, $currentUser);
            }
        }

        return $this->render('personal-account/my-transactions.html.twig', [
            'user' => $currentUser,
            'transactions' => $currentUser->getUserTransactions(),
        ]);
    }

    #[Route('/settlement-day')]
    public function settlementDay(): Response
    {
        return $this->userAccountService->immitateSettlementDay();
    }

    #[Route('/deleteservice/{id<\d+>}')]
    public function deleteService(int $id): Response
    {
        return $this->userAccountService->deleteServiceById($id);
    }
}
