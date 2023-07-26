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


    #[Route('/')]
    public function myServices(UserAccountRepository $userAccountRepository,
                               Request $request,
                               UserAccountService $userAccountService ): Response
    {
        $currentUser = $userAccountRepository->find(1);

        if ($request->getMethod() === 'POST')
        {
            $userAccountService->addAndShowUserServices($request,$currentUser);
        }

        return $this->render('personal-account/my-services.html.twig',[
            'user' => $currentUser,
            'services' => $currentUser->getUserServices()
        ]);
    }

    #[Route('/transactions')]
    public function myTransactions(UserAccountRepository $userAccountRepository,
                                  Request $request, UserAccountService $userAccountService): Response
    {
        $currentUser = $userAccountRepository->find(1);
        $transactions = $currentUser->getUserTransactions();

        if ($request->getMethod() === 'POST')
        {
            if(count($request->request->all()) === 3)
            {
                $transactions = $userAccountService->sortTransactionsByDateOrName($request,$currentUser->getId());
            }else{
                $userAccountService->addMoneyToUserBalance($request,$currentUser);
            }
        }

        return $this->render('personal-account/my-transactions.html.twig',[
            'user' => $currentUser,
            'transactions' => $transactions
        ]);
    }

    #[Route('/settlement-day')]
    public function settlementDay(UserAccountService $userAccountService): Response
    {
        return $userAccountService->immitateSettlementDay();
    }

    #[Route('/deleteservice/{id<\d+>}')]
    public function deleteService(int $id,UserAccountService $userAccountService): Response
    {
        return $userAccountService->deleteServiceById($id);
    }






}
