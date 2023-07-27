<?php

namespace App\Controller;

use App\Repository\UserAccountRepository;
use App\Service\UserAccountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PersonalAccountController extends AbstractController
{

    public function __construct(private UserAccountRepository $userAccountRepository,
                                private UserAccountService $userAccountService )
    {
    }

    #[Route('/')]
    public function myServices(Request $request): Response
    {
        $currentUser = $this->userAccountRepository->find(1);

        if ($request->getMethod() === 'POST')
        {
            $this->userAccountService->addAndShowUserServices($request,$currentUser);
        }

        return $this->render('personal-account/my-services.html.twig',[
            'user' => $currentUser,
            'services' => $currentUser->getUserServices()
        ]);
    }

    #[Route('/transactions')]
    public function myTransactions(Request $request): Response
    {
        $currentUser = $this->userAccountRepository->find(1);
        $transactions = $currentUser->getUserTransactions();

        if ($request->getMethod() === 'POST')
        {
            if(count($request->request->all()) === 3)
            {
                $transactions = $this->userAccountService->sortTransactionsByDateOrName($request,$currentUser->getId());
            }else{
                $this->userAccountService->addMoneyToUserBalance($request,$currentUser);
            }
        }

        return $this->render('personal-account/my-transactions.html.twig',[
            'user' => $currentUser,
            'transactions' => $transactions
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
