<?php

namespace Deimos\Providers;

use Curl\Curl;
use Deimos\Slice\Slice;

class Poloniex
{

    /**
     * @var Slice
     */
    protected $slice;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var string
     */
    protected $tradingUrl = 'https://poloniex.com/tradingApi';

    /**
     * @var string
     */
    protected $publicUrl = 'https://poloniex.com/public';

    /**
     * Poloniex constructor.
     *
     * @param Slice $slice
     */
    public function __construct(Slice $slice)
    {
        $this->slice  = $slice;
        $this->key    = $slice->getRequired('key');
        $this->secret = $slice->getRequired('secret');
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function hMac(array &$data)
    {
        $time          = explode(' ', microtime());
        $data['nonce'] = $time[1] . substr($time[0], 2, 6);
        $httpQuery     = http_build_query($data, '', '&');

        return hash_hmac('sha512', $httpQuery, $this->secret);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function headers(array &$data)
    {
        return [
            'Key'  => $this->key,
            'Sign' => $this->hMac($data),
        ];
    }

    /**
     * @return Curl
     */
    protected function curl()
    {
        if (!$this->curl)
        {
            $this->curl = new Curl();

            $this->curl->setOpt(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);

            $this->curl->setReferer($this->publicUrl);
            $this->curl->setUserAgent($this->userAgent());
        }

        return $this->curl;
    }

    /**
     * @return string
     */
    protected function userAgent()
    {
        return 'Mozilla/5.0 (compatible; Deimos Project; ' . php_uname('a') . '; PHP/' . phpversion() . ')';
    }

    /**
     * @param array $queries
     *
     * @return array
     */
    protected function retrieve(array $queries = [])
    {
        $curl = $this->curl()->get($this->publicUrl, $queries);

        return $this->response($curl);
    }

    /**
     * @param Curl $curl
     *
     * @return mixed
     */
    protected function response(Curl $curl)
    {
        return json_decode($curl->response, true);
    }

    /**
     * @param array $response
     *
     * @return array
     */
    protected function query(array $response)
    {
        $headers = $this->headers($response);

        $curl = clone $this->curl();

        foreach ($headers as $headerType => $headerValue)
        {
            $curl->setHeader($headerType, $headerValue);
        }

        $curl->post($this->tradingUrl, $response);

        return $this->response($curl);
    }

    /**
     * @param string $pair
     * @param string $rate
     * @param string $amount
     *
     * @return array
     */
    public function buy($pair, $rate, $amount)
    {
        return $this->query([
            'command'      => 'buy',
            'currencyPair' => strtoupper($pair),
            'rate'         => $rate,
            'amount'       => $amount
        ]);
    }

    /**
     * @param string $pair
     * @param string $rate
     * @param string $amount
     *
     * @return array
     */
    public function sell($pair, $rate, $amount)
    {
        return $this->query([
            'command'      => 'sell',
            'currencyPair' => strtoupper($pair),
            'rate'         => $rate,
            'amount'       => $amount
        ]);
    }

    /**
     * @param string $pair
     * @param string $orderNumber
     *
     * @return array
     */
    public function cancelOrder($pair, $orderNumber)
    {
        return $this->query([
            'command'      => 'cancelOrder',
            'currencyPair' => strtoupper($pair),
            'orderNumber'  => $orderNumber
        ]);
    }

    /**
     * @param string $currency
     * @param string $amount
     * @param string $address
     *
     * @return array
     */
    public function withdraw($currency, $amount, $address)
    {
        return $this->query([
            'command'  => 'withdraw',
            'currency' => strtoupper($currency),
            'amount'   => $amount,
            'address'  => $address
        ]);
    }

    /**
     * @param string $pair
     *
     * @return array
     */
    public function getTradeHistory($pair)
    {
        return $this->retrieve([
            'command'      => 'returnTradeHistory',
            'currencyPair' => strtoupper($pair)
        ]);
    }

    /**
     * @param string $pair
     *
     * @return array
     */
    public function getOrderBook($pair)
    {
        return $this->retrieve([
            'command'      => 'returnOrderBook',
            'currencyPair' => strtoupper($pair)
        ]);
    }

    /**
     * @return array
     */
    public function getVolume()
    {
        return $this->retrieve([
            'command' => 'return24hVolume'
        ]);
    }

    /**
     * @param string $pair
     *
     * @return array
     */
    public function getMyTradeHistory($pair)
    {
        return $this->query([
            'command'      => 'returnTradeHistory',
            'currencyPair' => strtoupper($pair)
        ]);
    }

    /**
     * @param string $pair
     *
     * @return array
     */
    public function getOpenOrders($pair)
    {
        return $this->query([
            'command'      => 'returnOpenOrders',
            'currencyPair' => strtoupper($pair)
        ]);
    }

    /**
     * @param $coin
     *
     * @return double
     */
    public function getBtcBalance($coin)
    {
        $balances = $this->query(['command' => 'returnCompleteBalances']);

        foreach ($balances as $key => $balance)
        {
            if ($key === $coin)
            {
                return $balance['btcValue'];
            }
        }

        return 0.;
    }

    /**
     * @param array $exclude
     *
     * @return double
     */
    public function getTotalBtcBalance(array $exclude = [])
    {
        $total = 0.;

        $balances = $this->query(['command' => 'returnCompleteBalances']);

        foreach ($balances as $key => $balance)
        {
            if (in_array($key, $exclude, true))
            {
                continue;
            }

            $total += $balance['btcValue'];
        }

        return $total;
    }

    /**
     * @param string $coin
     *
     * @return double
     */
    public function getBalance($coin)
    {
        $coin = strtoupper($coin);

        foreach ($this->getBalances() as $_coin => $balance)
        {
            if ($coin === $_coin)
            {
                return $balance;
            }
        }

        return .0;
    }

    /**
     * @param bool $withoutZero
     *
     * @return array
     */
    public function getBalances($withoutZero = true)
    {
        $balances = $this->query(['command' => 'returnBalances']);

        if ($withoutZero)
        {
            return array_filter($balances, function ($value)
            {
                return $value > 1E-10;
            });
        }

        return $balances;
    }

    /**
     * @return array
     */
    public function getReturnTicker()
    {
        return $this->retrieve(['command' => 'returnTicker']);
    }

    /**
     * @return array
     */
    public function getTradingPairs()
    {
        return array_keys($this->getReturnTicker());
    }

}
