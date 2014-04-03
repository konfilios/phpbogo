<?php
/*
 */

require('bootstrap.php');

use \BogoTree;

$generator = new \BogoTree\Generator;

$treeObjects = array(
	'N1' => array(
		'N1.1',
		'N1.2',
		'N1.3' => array(
			'N1.3.1',
			'N1.3.2' => array(
				'N1.3.2.1',
				'N1.3.2.2' => array(
					'N1.3.2.2.1'
				),
			),
		),
		'N1.4' => array(
			'N1.4.1',
			'N1.4.2',
			'N1.4.3' => array(
				'N1.4.3.1',
				'N1.4.3.2'
			)
		)
	),
	'N2'
);

function feedGenerator(\BogoTree\Generator $generator, $objects, $parentId)
{
	foreach ($objects as $i=>$j) {

		if (is_array($j)) {
			$nodeId = $object = $i;
			$childObjects = $j;
		} else {
			$nodeId = $object = $j;
			$childObjects = array();
		}

		if (!empty($parentId)) {
			$generator->addNode($nodeId, $nodeId, $parentId);
		} else {
			$generator->addRootNode($nodeId, $nodeId);
		}

		if (!empty($childObjects)) {
			feedGenerator($generator, $childObjects, $nodeId);
		}
	}
}

feedGenerator($generator, $treeObjects, null);

$imTree = new \BogoTree\Immutable\Tree($generator);
$i = new RecursiveIteratorIterator($imTree, RecursiveIteratorIterator::SELF_FIRST);
foreach ($i as $node) {
	echo str_repeat("   ", $i->getDepth()).$node->getId()."\n";
}
