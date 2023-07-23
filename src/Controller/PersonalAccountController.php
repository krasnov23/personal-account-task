<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonalAccountController extends AbstractController
{

    #[Route('/', name: 'app_my_services')]
    public function myServices(): Response
    {
        return $this->render('personal-account/my-services.html.twig');
    }

    #[Route('/transactions', name: 'app_my_transactions')]
    public function myTransactions(): Response
    {
        return $this->render('personal-account/my-transactions.html.twig');
    }


}
