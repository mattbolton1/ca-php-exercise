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
    private $averagePrice = 0.00;
    private $totalQty = 0;
    private $averageProfitMargin = 0.00;
    private $totalProfitUSD = 0.00;
    private $totalProfitCAD = 0.00;
    private $skuPosition = 0;
    private $costPosition = 0;
    private $pricePosition = 0;
    private $qtyPosition = 0;
    private $cadConversionRate = 1;

    function __construct()
    {
        $jsonResponse = file_get_contents("http://api.fixer.io/latest?base=USD&symbols=CAD");
        $rateConversion = json_decode($jsonResponse, true);
        $this->cadConversionRate = $rateConversion['rates']['CAD'];
    }

    public function processFile($postFieldName)
    {
        //Take uploaded file and separate into headers and data
        if ($_FILES[$postFieldName]['error'] == UPLOAD_ERR_OK      //No errors with upload
            && is_uploaded_file($_FILES[$postFieldName]['tmp_name'])  //uploaded file exists
        ) {
            $textFile = $_FILES[$postFieldName]['tmp_name'];
            $fileHandle = fopen($textFile,'r');
            $firstLine = true;
            if ($fileHandle) {
                while (($fileBuffer = fgets($fileHandle, 4096)) !== false) {// get each line
                    if ($firstLine) {
                        $fileHeaders = explode(",", $fileBuffer);
                        $this->setColumnPositions($fileHeaders);
                        $firstLine = false;
                    } else {
                        $this->fileData[] = explode(",", $fileBuffer);
                    }
                }
            }

            return true;
        } else {
            return false;
        }
    }

    private function setColumnPositions($columns)
    {
        foreach ($columns as $columnIndex => $columnName) {
            switch (trim($columnName)) {
                case 'sku':
                    $this->skuPosition = $columnIndex;
                    break;
                case 'cost':
                    $this->costPosition = $columnIndex;
                    break;
                case 'price':
                    $this->pricePosition = $columnIndex;
                    break;
                case 'qty':
                    $this->qtyPosition = $columnIndex;
                    break;
            }
        }
    }

    public function outputTable()
    {


        $htmlOutput = "";
        if(!count($this->fileData)){
            return "<h3>No data was detected</h3>";
        }

        //Send out HTML table with the data and calculated values
        $htmlOutput .= <<< HTML
        <table id="dataTable" summary="Data Results">
            <thead>
                <tr>
                    <th>SKU</th><th>Cost</th><th>Price</th><th>QTY</th>
                    <th>Profit Margin</th><th>Total Profit (USD)</th>
                    <th>Total Profit (CAD)</th>
                </tr>
            </thead>
            <tbody>
HTML;
        $totalPrices = 0;
        $totalProfitMargins = 0;
        foreach ($this->fileData as $dataRow) {

            $rowSKU = $dataRow[$this->skuPosition];
            $rowCost = $dataRow[$this->costPosition];

            $rowPrice = $dataRow[$this->pricePosition];
            $totalPrices += $rowPrice;

            $rowQty = $dataRow[$this->qtyPosition];
            $this->totalQty += $rowQty;

            $rowProfitMargin = $this->calculateProfitMargin($rowCost, $rowPrice);
            $totalProfitMargins += $rowProfitMargin;

            $rowTotalProfit = $this->calculateTotalProfitUSD($rowCost, $rowPrice, $rowQty);
            $this->totalProfitUSD += $rowTotalProfit;

            $rowTotalProfitCAD = $this->calculateTotalProfitCAD($rowTotalProfit);
            $this->totalProfitCAD += $rowTotalProfitCAD;

            $qtyClass = ($rowQty>0)?"upVal":"downVal";
            $marginClass = ($rowProfitMargin>0)?"upVal":"downVal";
            $profitClass = ($rowTotalProfit>0)?"upVal":"downVal";

            $htmlOutput .= "<tr>";
            $htmlOutput .= "<td>".$rowSKU."</td>";
            $htmlOutput .= "<td>".$this->formatCurrency($rowCost)."</td>";
            $htmlOutput .= "<td>".$this->formatCurrency($rowPrice)."</td>";
            $htmlOutput .= "<td class='".$qtyClass."'>".$rowQty."</td>";
            $htmlOutput .= "<td class='".$marginClass."'>".$this->formatPercent($rowProfitMargin)."</td>";
            $htmlOutput .= "<td class='".$profitClass."'>".$this->formatCurrency($rowTotalProfit)."</td>";
            $htmlOutput .= "<td class='".$profitClass."'>".$this->formatCurrency($rowTotalProfitCAD)."</td>";
            $htmlOutput .= "</tr>";
        }

        $this->averagePrice = $totalPrices/count($this->fileData);
        $this->averageProfitMargin = $totalProfitMargins/count($this->fileData);

        $htmlOutput .= <<< HTML
            </tbody>
            <tfoot>
                <tr>
                    <th rowspan="2" colspan="2">Summary</th>
                    <th>Average Price</th><th>Total QTY</th>
                    <th>Average Profit Margin</th><th>Total Profit (USD)</th>
                    <th>Total Profit (CAD)</th>
                </tr>
                <tr>
HTML;
        $htmlOutput .= "<td>".$this->formatCurrency($this->averagePrice)."</td>";
        $htmlOutput .= "<td>".$this->totalQty."</td>";
        $htmlOutput .= "<td>".$this->formatPercent($this->averageProfitMargin)."</td>";
        $htmlOutput .= "<td>".$this->formatCurrency($this->totalProfitUSD)."</td>";
        $htmlOutput .= "<td>".$this->formatCurrency($this->totalProfitCAD)."</td>";

        $htmlOutput .= <<< HTML
                </tr>
            </tfoot>
        </table>
HTML;

        return $htmlOutput;
    }

    private function formatCurrency($number)
    {
        return "$" . round(($number), 2);
    }

    private function formatPercent($number)
    {
        return round(($number * 100), 1) . "%";
    }

    private function calculateProfitMargin($Cost, $Price)
    {
        //Make sure Price is not Zero so we don't get a divide by zero error
        if ($Price == 0) {
            $Price = .01;
        }
        $profitMargin = (floatval($Price) - floatval($Cost)) / floatval($Price);
        return $profitMargin;
    }

    private function calculateTotalProfitUSD($Cost, $Price, $Qty)
    {
        $totalProfit = (floatval($Qty) * (floatval($Price) - floatval($Cost)));
        return $totalProfit;
    }

    private function calculateTotalProfitCAD($USD_Profit)
    {
        $cadProfit = $this->cadConversionRate*$USD_Profit;
        return $cadProfit;
    }
}