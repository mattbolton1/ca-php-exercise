<?php

/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 6/15/2017
 * Time: 2:50 PM
 */
class fileProcessor
{
    private $fileData = array();
    private $fileHeaders = array();//sku,price,qty,cost
    private $averagePrice = 0.00;
    private $totalQty = 0;
    private $averageProfitMargin = 0.00;
    private $totalProfitUSD = 0.00;
    private $totalProfitCAD = 0.00;

    /**
     * @return float
     */
    public function getAveragePrice()
    {
        return $this->averagePrice;
    }

    /**
     * @return int
     */
    public function getTotalQty()
    {
        return $this->totalQty;
    }

    /**
     * @return float
     */
    public function getAverageProfitMargin()
    {
        return $this->averageProfitMargin;
    }

    /**
     * @return float
     */
    public function getTotalProfitUSD()
    {
        return $this->totalProfitUSD;
    }

    /**
     * @return float
     */
    public function getTotalProfitCAD()
    {
        return $this->totalProfitCAD;
    }

    public function processFile($postFieldName)
    {
        //Take uploaded file and separate into headers and data
    }

    public function outputTable()
    {
        //Send out HTML table with the data and calculated values
    }

    private function calculateProfitMargin($Cost, $Price)
    {
        //
    }

    private function calculateTotalProfitUSD($Cost, $Price, $Qty)
    {
        //
    }

    private function calculateTotalProfitCAD($USD_Profit)
    {
        //
    }
}