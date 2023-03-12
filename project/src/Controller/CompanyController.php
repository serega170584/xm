<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CompanyHistory;
use App\Repository\CompanyHistoryRepository;
use App\Repository\CompanyRepository;
use App\Service\CompanyFieldsValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/company', name: 'app_company')]
    public function index(Request $request, CompanyFieldsValidator $companyFieldsValidator, CompanyRepository $repository, MailerInterface $mailer): Response
    {
        $errorMessages = [];

        $values = [
            'symbol' => '',
            'start_date' => '',
            'end_date' => '',
            'email' => '',
        ];

        $token = $request->get('token');
        if ($request->get('submit') === 'Send' && $this->isCsrfTokenValid('company-order', $token)) {

            $companyFieldsValidator->validate($request);
            $errorMessages = $companyFieldsValidator->getErrorMessages();

            $company = $repository->findOneBySymbol($request->get('symbol'));

            if ([] === $errorMessages) {
                $email = (new Email())
                    ->from($this->getParameter('from_email'))
                    ->to($request->get('email'))
                    ->subject($company->getCompanyName())
                    ->text('From ' . $request->get('start_date') . ' to ' . $request->get('end_date'));

                $mailer->send($email);

                return $this->redirect('history?symbol=' . $request->get('symbol') . '&start_date=' . $request->get('start_date') . '&end_date=' . $request->get('end_date'));
            }
        }
        return $this->render('company/index.html.twig', ['errors_text' => implode(' ', $errorMessages)] + $values);
    }

    #[Route('/history', name: 'app_history')]
    public function history(Request $request, CompanyHistoryRepository $repository): Response
    {
        $history = $repository->findAllBySymbolDates($request->get('symbol'), $request->get('start_date'), $request->get('end_date'));

        $labels = [];
        $openData = [];
        $closeData = [];
        /**
         * @var CompanyHistory $item
         */
        foreach ($history as $item) {
            $date = $item->getDate()->format('Y-m-d');
            $labels[] = $date;
            $openItem['x'] = $date;
            $openItem['y'] = $item->getOpen();
            $openData[] = $openItem;
            $closeItem['x'] = $date;
            $closeItem['y'] = $item->getClose();
            $closeData[] = $closeItem;
        }

        return $this->render('company/history.html.twig', [
            'history' => $history,
            'labels' => json_encode($labels),
            'open_data' => json_encode($openData),
            'close_data' => json_encode($closeData),
        ]);
    }
}
