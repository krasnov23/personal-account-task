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
                                private Request $request,
                                private UserAccountService $userAccountService )
    {
    }

    #[Route('/')]
    public function myServices(): Response
    {
        $currentUser = $this->userAccountRepository->find(1);

        if ($this->request->getMethod() === 'POST')
        {
            $this->userAccountService->addAndShowUserServices($this->request,$currentUser);
        }

        return $this->render('personal-account/my-services.html.twig',[
            'user' => $currentUser,
            'services' => $currentUser->getUserServices()
        ]);
    }

    #[Route('/transactions')]
    public function myTransactions(): Response
    {
        $currentUser = $this->userAccountRepository->find(1);
        $transactions = $currentUser->getUserTransactions();

        if ($this->request->getMethod() === 'POST')
        {
            if(count($this->request->request->all()) === 3)
            {
                $transactions = $this->userAccountService->sortTransactionsByDateOrName($this->request,$currentUser->getId());
            }else{
                $this->userAccountService->addMoneyToUserBalance($this->request,$currentUser);
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
