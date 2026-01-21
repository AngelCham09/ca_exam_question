<?php

namespace App\Http\Controllers;

class GraphController extends Controller
{
    public function index()
    {
        /*
         *  Todo: calculate Sharpe Ratio, Calmar Ratio, MDD, Annual Return here
         *  Todo: Make sure the next page has a graph
         *
         */
        $path = public_path('sample_data.csv');
        $records = collect();

        if (file_exists($path) && ($handle = fopen($path, "r")) !== FALSE) {
            $header = fgetcsv($handle);
            $columns = array_flip($header);

            while (($row = fgetcsv($handle)) !== FALSE) {
                $records->push((object)[
                    'pnl'    => (float)($row[$columns['pnl']] ?? 0),
                    'dd'     => (float)($row[$columns['dd']] ?? 0),
                    'date'   => $row[$columns['date']] ?? '',
                    'equity' => (float)($row[$columns['equity']] ?? 0),
                ]);
            }

            fclose($handle);
        }

        // Mean of PnL
        $meanPnl = $records->avg('pnl');

        // Standard Deviation of PnL
        $variance = $records->map(fn($r) => pow($r->pnl - $meanPnl, 2))->avg();
        $stdDevPnl = sqrt($variance);

        //Annual Return = mean of Pnl x 365
        $annualReturn = $meanPnl * 365;

        //Sharpe Ratio = (mean of pnl / standard deviation of pnl) x square root of 365
        $sharpeRatio = ($stdDevPnl != 0) ? ($meanPnl / $stdDevPnl) * sqrt(365) : 0;

        //Maximum Drawdown = Max of DD
        $maxDrawdown = $records->max('dd');

        //Calmar Ratio = Annual Return / |Maximum Drawdown|
        $calmarRatio = ($maxDrawdown != 0) ? ($annualReturn / abs($maxDrawdown)) : 0;

        return view('graph.index', [
            'labels'  => $records->pluck('date'),
            'equity'  => $records->pluck('equity'),
            'metrics' => [
                'annual_return' => number_format($annualReturn * 100, 2) . '%',
                'sharpe'        => number_format($sharpeRatio, 2),
                'max_dd'        => number_format($maxDrawdown * 100, 2) . '%',
                'calmar'        => number_format($calmarRatio, 2),
            ]
        ]);
    }
}
