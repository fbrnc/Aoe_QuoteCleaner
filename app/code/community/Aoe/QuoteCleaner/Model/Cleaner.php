<?php

class Aoe_QuoteCleaner_Model_Cleaner {

	/**
	 * Clean old quote entries.
	 * This method will be called via a Magento crontab task.
	 *
	 * @param void
	 * @return void
	 */
	public function clean() {

		$report = array();

		$limit = intval(Mage::getStoreConfig('system/quotecleaner/limit'));
		$limit = min($limit, 50000);

		$olderThan = intval(Mage::getStoreConfig('system/quotecleaner/clean_quoter_older_than'));
		$olderThan = max($olderThan, 7);

		$writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write'); /* @var $writeConnection Varien_Db_Adapter_Pdo_Mysql */

		$tableName = Mage::getSingleton('core/resource')->getTableName('sales/quote');
		$tableName = $writeConnection->quoteIdentifier($tableName, true);



		// customer quotes
		$startTime = time();
		$sql = sprintf('DELETE FROM %s WHERE NOT ISNULL(customer_id) AND updated_at < DATE_SUB(Now(), INTERVAL %s DAY) LIMIT %s',
			$tableName,
			$olderThan,
			$limit
		);
		$stmt = $writeConnection->query($sql);
		$report['customer']['count'] = $stmt->rowCount();
		$report['customer']['duration'] = time() - $startTime;
		Mage::log('[QUOTECLEANER] Cleaning old customer quotes (duration: '.$report['customer']['duration'].', row count: '.$report['customer']['count'].')');

		// anonymous quotes
                $olderThan = intval(Mage::getStoreConfig('clean_anonymous_quotes_older_than'));
                $olderThan = max($olderThan, 7);
                $startTime = time();
		$sql = sprintf('DELETE FROM %s WHERE ISNULL(customer_id) AND updated_at < DATE_SUB(Now(), INTERVAL %s DAY) LIMIT %s',
			$tableName,
			$olderThan,
			$limit
		);
		$stmt = $writeConnection->query($sql);
		$report['anonymous']['count'] = $stmt->rowCount();
		$report['anonymous']['duration'] = time() - $startTime;
		Mage::log('[QUOTECLEANER] Cleaning old anonymous quotes (duration: '.$report['anonymous']['duration'].', row count: '.$report['anonymous']['count'].')');



		return $report;
	}

}
