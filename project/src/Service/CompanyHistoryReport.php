<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CompanyHistory;
use App\Repository\CompanyHistoryRepository;
use Symfony\Component\HttpFoundation\Request;

class CompanyHistoryReport
{
    private array $labels = [];

    private array $openData = [];

    private array $closeData = [];

    private array $history;

    public function generate(Request $request, CompanyHistoryRepository $repository): void
    {
        $history = $repository->findAllBySymbolDates($request->get('symbol'), $request->get('start_date'), $request->get('end_date'));

        /**
         * @var CompanyHistory $item
         */
        foreach ($history as $item) {
            $date = $item->getDate()->format('Y-m-d');
            $this->labels[] = $date;
            $openItem['x'] = $date;
            $openItem['y'] = $item->getOpen();
            $this->openData[] = $openItem;
            $closeItem['x'] = $date;
            $closeItem['y'] = $item->getClose();
            $this->closeData[] = $closeItem;
        }

        $this->history = $history;

    }

    /**
     * @return array
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @return array
     */
    public function getOpenData(): array
    {
        return $this->openData;
    }

    /**
     * @return array
     */
    public function getCloseData(): array
    {
        return $this->closeData;
    }

    /**
     * @return array
     */
    public function getHistory(): array
    {
        return $this->history;
    }
}