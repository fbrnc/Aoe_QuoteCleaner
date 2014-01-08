<?php

require_once 'abstract.php';

class Aoe_Shell_QuoteCleaner extends Mage_Shell_Abstract
{
    /**
     * Run script
     */
    public function run()
    {
        $quoteDeleteLimit = null;
        if ($this->getArg('limit') && ((int) $this->getArg('limit') > 0)) {
            $quoteDeleteLimit = (int) $this->getArg('limit');
        }

        echo "Start Aoe_QuoteCleaner\r\n";
        Mage::getModel("aoe_quotecleaner/cleaner")->clean(null, $quoteDeleteLimit);
        echo "End Aoe_QuoteCleaner\r\n";
    }

    /**
     * Retrieve Usage Help Message
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f aoe_quotecleaner.php -- [options]

  --limit <delete_limit>        Delete quote limit (max 50.000)
  help                          This help

USAGE;
    }
}

//Run Aoe QuoteCleaner Script
$shell = new Aoe_Shell_QuoteCleaner();
$shell->run();

