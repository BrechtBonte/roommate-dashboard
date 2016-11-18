<?php

namespace RoommateBundle\Controller;

use RoommateBundle\Entity\Debt\Transaction;
use RoommateBundle\Entity\Roommate\Roommate;
use RoommateBundle\Form\TransactionType;
use RoommateBundle\Provider\AuthenticatedUser;
use RoommateBundle\Repositories\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DebtController extends Controller
{
    public function viewAction()
    {
        $roommateId = $this->getCurrentRoommateId();
        $transactionRepo = $this->getTransactionRepository();

        $recentTransactions = $transactionRepo->fetchRecentTransactions($roommateId);
        $contacts = $transactionRepo->fetchContacts($roommateId);

        $contactNames = array_column($contacts, 'name');
        $totalBalance = $transactionRepo->fetchTotalBalance($roommateId);

        $form = $this->createForm(TransactionType::class);

        return $this->render('RoommateBundle:Debt:view.html.twig', [
            'recentTransactions' => $recentTransactions,
            'contacts' => $contacts,
            'contactNames' => $contactNames,
            'totalBalance' => $totalBalance,
            'form' => $form->createView(),
        ]);
    }

    public function addAction(Request $request)
    {
        $roommateId = $this->getCurrentRoommateId();
        $transactionRepo = $this->getTransactionRepository();

        $recentTransactions = $transactionRepo->fetchRecentTransactions($roommateId);
        $contacts = $transactionRepo->fetchContacts($roommateId);

        $contactNames = array_column($contacts, 'name');
        $totalBalance = $transactionRepo->fetchTotalBalance($roommateId);

        $form = $this->createForm(TransactionType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render('RoommateBundle:Debt:view.html.twig', [
                'recentTransactions' => $recentTransactions,
                'contacts' => $contacts,
                'contactNames' => $contactNames,
                'totalBalance' => $totalBalance,
                'form' => $form->createView(),
            ]);
        }

        // handle
        $amount = preg_replace('/[^0-9,]/', '', $form->get('amount')->getData());
        $parts = explode(',', $amount);
        $amount = $parts[0] * 100 + (int)substr(($parts[1] ?? '0') . '0', 0, 2);
        $amount *= $form->get('multiplier')->getData();

        $manager = $this->getDoctrine()->getManager();
        $transactionRepo->add(
            new Transaction(
                $manager->find(Roommate::class, $this->getCurrentRoommateId()),
                $form->get('to')->getData(),
                $amount,
                $form->get('description')->getData() ?: null
            )
        );
        $manager->flush();

        $this->addFlash('success', 'Added transaction');

        return $this->redirectToRoute('debt_overview');
    }

    public function contactAction($contact)
    {
        $transaction = $this->getTransactionRepository()->fetchTransactionsForContact(
            $this->getCurrentRoommateId(),
            $contact
        );

        return $this->render('RoommateBundle:Debt:contact.html.twig', [
            'transactions' => $transaction,
        ]);
    }

    private function getTransactionRepository() : TransactionRepository
    {
        return $this->getDoctrine()->getManager()->getRepository(Transaction::class);
    }

    private function getCurrentRoommateId()
    {
        /** @var AuthenticatedUser $user */
        $user = $this->getUser();
        return $user->getRoommateId();
    }
}
