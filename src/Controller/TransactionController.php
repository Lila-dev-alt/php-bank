<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Message;
use App\Entity\User;
use App\Form\AddMoneyType;
use App\Form\AskUserType;
use App\Form\SelectAccountType;
use App\Form\TransferMoneyType;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

class TransactionController extends AbstractController
{
    #[Route('/accounts/transaction/{accountId}', name: 'transaction-account')]
    public function virement(ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager,  $accountId, UserInterface $user): Response
    {

        $uuid = Uuid::fromString($accountId);

        $accountSingle = $doctrine->getRepository(Account::class)->findOneBy(['accountId' => $uuid]);
        if($accountSingle->GetUserId()->getId() != $user->getId()){
            return $this->redirectToRoute('accounts');
        }
        $form = $this->createForm(TransferMoneyType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && (float)$form->getData()["money"]<= $accountSingle ->getMoney()) {
            $recipientid = $form->getData()["recipient"];
            $recipient = $doctrine->getRepository(Account::class)->find($recipientid);

            $recipient->setMoney((float)$form->getData()["money"] + $recipient ->getMoney());
            $accountSingle->setMoney(-(float)$form->getData()["money"] + $accountSingle ->getMoney());

            $entityManager->persist($accountSingle);
            $entityManager->persist($recipient);
            $entityManager->flush();

            return $this->redirectToRoute('accounts');
        }
        return $this->renderForm('transaction/index.html.twig', [
            'form' => $form,
        ]);
    }

}
