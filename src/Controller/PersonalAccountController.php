<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonalAccountController extends AbstractController
{

    #[Route('/')]
    public function myServices(): Response
    {
        return $this->render('personal-account/my-services.html.twig',[
            
        ]);
    }

    #[Route('/transactions')]
    public function myTransactions(): Response
    {
        return $this->render('personal-account/my-transactions.html.twig',[

        ]);
    }


}
