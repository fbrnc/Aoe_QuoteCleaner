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
		$startTime = time();

		$limit = intval(Mage::getStoreConfig('system/quotecleaner/limit'));
		$limit = min($limit, 50000);

		$olderThan = intval(Mage::getStoreConfig('system/quotecleaner/clean_quoter_older_than'));
		$olderThan = max($olderThan, 7);

		$writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write'); /* @var $writeConnection Varien_Db_Adapter_Pdo_Mysql */

		$tableName = Mage::getSingleton('core/resource')->getTableName('sales/quote');

		$sql = sprintf('DELETE FROM %s WHERE updated_at < DATE_SUB(Now(), INTERVAL %s DAY) LIMIT %s',
			$writeConnection->quoteIdentifier($tableName, true),
			$olderThan,
			$limit
		);

		$stmt = $writeConnection->query($sql);
		$rowCount = $stmt->rowCount();

		$duration = time() - $startTime;
		Mage::log('[QUOTECLEANER] Cleaning old quotes (duration: '.$duration.', row count: '.$rowCount.')');
		return $rowCount;
	}

}
