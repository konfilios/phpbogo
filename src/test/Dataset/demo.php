<?php
/**
 */

// Or, using an anonymous function as of PHP 5.3.0
spl_autoload_register(function ($class) {
    include '../lib/'.$class. '.php';
});

/**
 * Generates a dataset of given item count.
 *
 * If $keyPrefix is specified, then an associative array is produced.
 * If not, a zero-based index array is produced.
 *
 * @param integer $itemCount
 * @param string $keyPrefix
 */
function generateDataset($itemCount, $keyPrefix = null)
{
	$dataset = array();
	for ($i = 0; $i < $itemCount; $i++) {
		if ($keyPrefix) {
			$dataset[$keyPrefix.$i] = 'Value '.$i;
		} else {
			$dataset[] = 'Value '.$i;
		}
	}

	return $dataset;
}


$gridClasses = array(
	'CBMaxColumnGrid',
	'CBFixedColumnGrid'
);

for ($itemCount = 1; $itemCount <= 30; $itemCount++) {
	$datasets = array(
//		'CBIndexedAssoc' => generateDataset($itemCount, 'key'),
		'CBIndexedArray' => generateDataset($itemCount)
	);

	foreach ($datasets as $datasetClass=>$dataset) {

		foreach ($gridClasses as $gridClass) {
			// Test for different column counts
			for ($columnCount = 1; $columnCount < 5; $columnCount++) {
				echo("<h1>$gridClass on $datasetClass with $itemCount items in $columnCount columns</h1>\n\n");

				// Create grid
				$grid = new $gridClass(new $datasetClass($dataset), $columnCount);

				if ($grid->getColumnCount() != $columnCount) {
					echo('<p style="color:red">Final grid has less columns than requested!</p>');
				}

				echo("<table border=\"1\">\n");
				foreach ($grid as $row) {
					echo("<tr>\n");
					foreach ($row as $key=>$item) {
						echo("<td>".(($item === null) ? "null" : "$key => $item")."</td>\n");
					}
					echo("</tr>\n");
				}

				echo("<table>\n");
			}
		}
	}
}