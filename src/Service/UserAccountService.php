<?php

namespace App\Service;

use App\Entity\ServiceInfo;
use App\Entity\Transaction;
use App\Entity\UserAccount;
use App\Repository\ServiceInfoRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserAccountRepository;
use DateTime;
use DateTimeImmutable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;


class UserAccountService
{
    public function __construct(private UserAccountRepository $userAccountRepository,
                                private TransactionRepository $transactionRepository,
                                private ServiceInfoRepository $serviceInfoRepository,
                                private RequestStack $requestStack,
                                private RouterInterface $router,
                                )
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function addAndShowUserServices(Request $request,UserAccount $userAccount): void
    {

        // Получили имя сервиса
        $serviceName = $request->request->all()['service-name'];

        // Количество указанного в сервисе
        $amountOfService = $request->request->all()['number-of-service'];

        // Получили цену данного наименования
        $priceOfSelectedService = $this->serviceInfoRepository->findServiceByNameAndPriceNotNull($serviceName)->getPrice();

        $compareTime = $this->compareTime();

        // умножили количество сервисов на цену выбранного сервиса и разделили на дни до первого числа
        $totalCoast = ceil($amountOfService * $priceOfSelectedService /  $compareTime[0] * $compareTime[1] );

        // Проверка что больше итоговая сумма или баланс пользователя
        if ($userAccount->getBalance() < $totalCoast)
        {
            //$this->addFlash('warning','Ваш баланс меньше чем нужная сумма');
            $this->requestStack->getSession()->getFlashBag()->add('warning','Ваш баланс меньше чем нужная сумма');
            //$this->redirectToRoute('services');
            new RedirectResponse($this->router->generate('services'),302);
        }else{

            // Ищем все сервисы которые есть у нашего пользователя
            $servicesOfUser = $this->serviceInfoRepository->findServicesNamesByUser($userAccount->getId());

            // Переменная в которую попадает значение в случае если имя сервиса уже есть у пользователя
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
                        $serviceOfUserById = $this->serviceInfoRepository->find($checkSameNames);

                        // Обновляем количество сервисов
                        $newAmountOfService = $serviceOfUserById->getAmount() + $amountOfService;

                        // Задаем новое значение в колонку количество
                        $serviceOfUserById->setAmount($newAmountOfService);

                        // Задаем новый баланс с вычетом
                        $userAccount->setBalance($userAccount->getBalance() - $totalCoast);

                        // Задаем нашему пользователю обновленный сервис
                        $userAccount->addUserService($serviceOfUserById);

                        // Обновляем пользователя
                        $this->userAccountRepository->save($userAccount,true);

                        // Если данного сервиса еще не было в сущности User, то создаем
                    }else{
                        $this->makeNewUserService($priceOfSelectedService,$amountOfService,$serviceName,
                                                    $totalCoast,$userAccount);
                    }
                    // Если список пуст, то добавляем новый сервис
                }else{
                    $this->makeNewUserService($priceOfSelectedService,$amountOfService,$serviceName,
                                        $totalCoast,$userAccount);
            }

                //$this->addFlash('success','Cервис успешно добавлен');
                $this->requestStack->getSession()->getFlashBag()->add('success','Сервис успешно добавлен');
                //$this->redirectToRoute('services');
                new RedirectResponse($this->router->generate('services'),302);
            }

            $transaction = new Transaction();
            $transaction->setServiceName($serviceName);
            $transaction->setDate(new DateTimeImmutable());
            $transaction->setTotalPrice($totalCoast);
            $transaction->setAccountBalance($userAccount->getBalance());
            $userAccount->addUserTransaction($transaction);
            $this->userAccountRepository->save($userAccount,true);
        }

    public function addMoneyToUserBalance(Request $request,UserAccount $currentUser): void
    {
        $newBalance = $currentUser->getBalance() + $request->request->all()['amount-to-add'];
        $currentUser->setBalance($newBalance);
        $this->userAccountRepository->save($currentUser,true);
        $this->requestStack->getSession()->getFlashBag()->add('success','Баланс успешно пополнен');
        new RedirectResponse($this->router->generate('transactions'),302);
    }

    public function sortTransactionsByDateOrName(Request $request,int $userId)
    {
        $allData = $request->request->all();
        $transactionByQuery = null;

        if ($allData['begin'] and $allData['finish'] and $allData['service-name'])
        {
            $transactionByQuery = $this->transactionRepository->sortTransactions($userId,$allData['begin'],
                $allData['finish'],$allData['service-name']);
        }elseif ($allData['begin'] and $allData['finish']){
            $transactionByQuery = $this->transactionRepository->sortTransactions($userId,$allData['begin'],
            $allData['finish']);
        }elseif ($allData['begin'] and $allData['service-name']){
            $transactionByQuery = $this->transactionRepository->sortTransactions($userId,$allData['begin'],
            null,$allData['service-name']);
        }elseif ($allData['finish'] and $allData['service-name']){
            $transactionByQuery = $this->transactionRepository->sortTransactions($userId,null,
            $allData['finish'],$allData['service-name']);
        }elseif ($allData['begin'])
        {
            $transactionByQuery = $this->transactionRepository->sortTransactions($userId,$allData['begin']);
        }elseif ($allData['finish'])
        {
            $transactionByQuery = $this->transactionRepository->sortTransactions($userId,null,
            $allData['finish']);
        }elseif ($allData['service-name'])
        {
            $transactionByQuery = $this->transactionRepository->sortTransactions($userId,null,
                null,$allData['service-name']);
        }else{
            $transactionByQuery = $this->transactionRepository->sortTransactions($userId);
        }

        return $transactionByQuery;
    }

    public function immitateSettlementDay(): RedirectResponse
    {
        $currentUser = $this->userAccountRepository->find(1);

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
            $this->requestStack->getSession()->getFlashBag()->add('warning','Недостаточно средств для оплаты всех услуг');
            return new RedirectResponse($this->router->generate('transactions'),302);
        }


        $currentUser->setBalance($currentUser->getBalance() - $totalPayment);
        $this->userAccountRepository->save($currentUser,true);
        $this->requestStack->getSession()->getFlashBag()->add('success','деньги за все услуги успешно списаны');
        return new RedirectResponse($this->router->generate('transactions'),302);
    }

    public function deleteServiceById($id): RedirectResponse
    {
        $currentUser = $this->userAccountRepository->find(1);

        $todayDate = new DateTimeImmutable();
        $firstNumberNextMonth = new DateTime();
        $firstNumberNextMonth->setTimestamp(strtotime("first day of next month"));
        $diff = $firstNumberNextMonth->diff($todayDate)->format("%a");
        $daysInMonth = date('t');

        $chooseService = $this->serviceInfoRepository->find($id);
        $servicePrice = $chooseService->getPrice();
        $serviceAmount = $chooseService->getAmount();
        $totalCoast = round($serviceAmount * $servicePrice / $daysInMonth * $diff );
        $currentUser->setBalance($currentUser->getBalance() + $totalCoast);
        $this->serviceInfoRepository->remove($chooseService,true);

        $this->requestStack->getSession()->getFlashBag()->add('success','Cервис успешно удален');
        return new RedirectResponse($this->router->generate('services'),302);
    }

    private function compareTime():array
    {
        $todayDate = new DateTimeImmutable();
        $firstNumberNextMonth = new DateTime();
        $firstNumberNextMonth->setTimestamp(strtotime("first day of next month"));
        $diff = $firstNumberNextMonth->diff($todayDate)->format("%a");
        $daysInMonth = date('t');

        return [$daysInMonth,$diff];
    }

    private function makeNewUserService(int $priceOfSelectedService,int $amountOfService,string $serviceName,
                                        int $totalCoast,UserAccount $userAccount): void
    {
        $newServiceInfo = new ServiceInfo();

        $newServiceInfo->setPrice($priceOfSelectedService);
        $newServiceInfo->setAmount($amountOfService);
        $newServiceInfo->setName($serviceName);
        $userAccount->setBalance($userAccount->getBalance() - $totalCoast);

        $userAccount->addUserService($newServiceInfo);

        $this->userAccountRepository->save($userAccount,true);
    }
}