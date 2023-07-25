<?php

namespace App\Controller;

use App\Entity\ServiceInfo;
use App\Entity\Transaction;
use App\Repository\ServiceInfoRepository;
use App\Repository\UserAccountRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PersonalAccountController extends AbstractController
{

    #[Route('/')]
    public function myServices(UserAccountRepository $userAccountRepository,
                               ServiceInfoRepository $serviceInfoRepository,
                               Request $request ): Response
    {
        $currentUser = $userAccountRepository->find(1);

        if ($request->getMethod() === 'POST')
        {

            // Получили имя сервиса
            $serviceName = $request->request->all()['service-name'];

            // Количество указанного в сервисе
            $amountOfService = $request->request->all()['number-of-service'];

            // Получили цену данного наименования
            $priceOfSelectedService = $serviceInfoRepository->findServiceByNameAndPriceNotNull($serviceName)->getPrice();

            $todayDate = new DateTimeImmutable();
            $firstNumberNextMonth = new DateTime();
            $firstNumberNextMonth->setTimestamp(strtotime("first day of next month"));
            $diff = $firstNumberNextMonth->diff($todayDate)->format("%a");
            $daysInMonth = date('t');

            // умножили количество сервисов на цену выбранного сервиса и разделили на дни до первого числа
            $totalCoast = ceil($amountOfService * $priceOfSelectedService /  $daysInMonth * $diff );

            // Проверка что больше итоговая сумма или баланс пользователя
            if ($currentUser->getBalance() < $totalCoast)
            {
                $this->addFlash('warning','Ваш баланс меньше чем нужная сумма');
                $this->redirectToRoute('services');
            }else{

                // Ищем все сервисы которые есть у нашего пользователя
                $servicesOfUser = $serviceInfoRepository->findServicesNamesByUser($currentUser->getId());

                // Переменная в которую попадает значение в случае если имя сервиса уже есть в пользователе
                $checkSameNames = null;


                // Проверяем если ли у нашего пользователя сервисы вообще
                if (!empty($servicesOfUser))
                {

                    // Перебираем все сервисы пользователя
                    foreach ($servicesOfUser as $serviceOfUser)
                    {
                        // Если находим уже имеющийся сервис, тогда изменяем его параметры
                        $serviceOfUser->getName() === $serviceName ? $checkSameNames = $serviceOfUser->getId() : $checkSameNames = null;
                        if ($checkSameNames)
                        {
                            break;
                        }
                    }

                    if ($checkSameNames)
                    {
                        $serviceOfUserById = $serviceInfoRepository->find($checkSameNames);

                        // Обновляем количество сервисов
                        $newAmountOfService = $serviceOfUserById->getAmount() + $amountOfService;

                        // Задаем новое значение в колонку количество
                        $serviceOfUserById->setAmount($newAmountOfService);

                        // Задаем новый баланс с вычетом
                        $currentUser->setBalance($currentUser->getBalance() - $totalCoast);

                        // Задаем нашему пользователю обновленный сервис
                        $currentUser->addUserService($serviceOfUserById);

                        // Обновляем пользователя
                        $userAccountRepository->save($currentUser,true);

                    // Если данного сервиса еще не было в сущности User, то создаем
                    }else{
                    $newServiceInfo = new ServiceInfo();

                    $newServiceInfo->setPrice($priceOfSelectedService);
                    $newServiceInfo->setAmount($amountOfService);
                    $newServiceInfo->setName($serviceName);
                    $currentUser->setBalance($currentUser->getBalance() - $totalCoast);

                    $currentUser->addUserService($newServiceInfo);

                    $userAccountRepository->save($currentUser,true);
                }
                // Если список пуст, то добавляем новый сервис
                }else{
                    $newServiceInfo = new ServiceInfo();
                    $newServiceInfo->setPrice($priceOfSelectedService);
                    $newServiceInfo->setAmount($amountOfService);
                    $newServiceInfo->setName($serviceName);

                    $currentUser->setBalance($currentUser->getBalance() - $totalCoast);

                    $currentUser->addUserService($newServiceInfo);

                    $userAccountRepository->save($currentUser,true);
                }

                $this->addFlash('success','Cервис успешно добавлен');
                $this->redirectToRoute('services');
            }

            $transaction = new Transaction();
            $transaction->setServiceName($serviceName);
            $transaction->setDate(new \DateTimeImmutable());
            $transaction->setTotalPrice($totalCoast);
            $transaction->setAccountBalance($currentUser->getBalance());
            $currentUser->addUserTransaction($transaction);
            $userAccountRepository->save($currentUser,true);
        }



        return $this->render('personal-account/my-services.html.twig',[
            'user' => $currentUser,
            'services' => $currentUser->getUserServices()
        ]);
    }

    #[Route('/transactions')]
    public function myTransactions(UserAccountRepository $userAccountRepository,
                                  Request $request): Response
    {
        $currentUser = $userAccountRepository->find(1);

        if ($request->getMethod() === 'POST')
        {
            $newBalance = $currentUser->getBalance() + $request->request->all()['amount-to-add'];
            $currentUser->setBalance($newBalance);
            $userAccountRepository->save($currentUser,true);
            $this->addFlash('success','Баланс пополнен успешно');
            $this->redirectToRoute('transactions');
        }

        return $this->render('personal-account/my-transactions.html.twig',[
            'user' => $currentUser
        ]);
    }

    #[Route('/settlement-day')]
    public function settlementDay(UserAccountRepository $userAccountRepository): Response
    {
        $currentUser = $userAccountRepository->find(1);

        $userServices = $currentUser->getUserServices();
        $totalPayment = 0;

        $todayDate = new DateTimeImmutable();
        $firstNumberNextMonth = new DateTime();
        $firstNumberNextMonth->setTimestamp(strtotime("first day of next month"));
        $diff = $firstNumberNextMonth->diff($todayDate)->format("%a");
        $dayInMonth = date('t');


        foreach ($userServices as $service)
        {
            $totalPayment += ceil($service->getPrice() * $service->getAmount() / $dayInMonth * $diff);
        }

        if ($totalPayment > $currentUser->getBalance())
        {
            $this->addFlash('warning','Недостаточно средства для оплаты всех услуг');
            return $this->redirectToRoute('transactions');
        }


        $currentUser->setBalance($currentUser->getBalance() - $totalPayment);
        $userAccountRepository->save($currentUser,true);
        $this->addFlash('success','деньги за все услуги успешно списаны');
        return $this->redirectToRoute('transactions');

    }

    #[Route('/deleteservice/{id<\d+>}')]
    public function deleteService(int $id,
                             ServiceInfoRepository $serviceInfoRepository,
                             UserAccountRepository $userAccountRepository): Response
    {
        $currentUser = $userAccountRepository->find(1);

        $todayDate = new DateTimeImmutable();
        $firstNumberNextMonth = new DateTime();
        $firstNumberNextMonth->setTimestamp(strtotime("first day of next month"));
        $diff = $firstNumberNextMonth->diff($todayDate)->format("%a");
        $daysInMonth = date('t');

        $chooseService = $serviceInfoRepository->find($id);
        $servicePrice = $chooseService->getPrice();
        $serviceAmount = $chooseService->getAmount();
        $totalCoast = round($serviceAmount * $servicePrice / $daysInMonth * $diff );
        $currentUser->setBalance($currentUser->getBalance() + $totalCoast);
        $serviceInfoRepository->remove($chooseService,true);

        $this->addFlash('success','Cервис успешно удален');
        return $this->redirectToRoute('services');

    }






}
