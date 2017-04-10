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
        $this->curl   = new Curl();
        $this->slice  = $slice;
        $this->key    = $slice->getRequired('key');
        $this->secret = $slice->getRequired('secret');
    }

    /**
     * @param array $queries
     *
     * @return array
     */
    protected function retrieve(array $queries = [])
    {
        $curl = $this->curl->get($this->publicUrl, $queries);

        return json_decode($curl->response, true);
    }

    protected function query(array $response)
    {

    }

    /**
     * @return array
     */
    public function returnTicker()
    {
        return $this->retrieve(['command' => 'returnTicker']);
    }

    /**
     * @return array
     */
    public function tradingPairs()
    {
        return array_keys($this->returnTicker());
    }

}