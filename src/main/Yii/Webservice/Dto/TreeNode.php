<?php
/*
 */

namespace Bogo\Yii\Webservice\Dto;
use Bogo\Yii\Webservice;

/**
 * Tree node.
 *
 * @since 1.2
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class TreeNode extends Webservice\Dto
{
	/**
	 * Wrapped data.
	 *
	 * @var mixed
	 */
	public $data;

	/**
	 * Child nodes.
	 *
	 * @var TreeNode[]
	 */
	public $children;

	/**
	 * Create an array of json tree nodes using passed nodeset.
	 *
	 * @param \BogoTree\INodeset $nodeset
	 * @return TreeNode[]
	 */
	static public function createFromNodeset(\BogoTree\INodeset $nodeset)
	{
		$jsonNode = new static();

		// Retrieve data type
		$attributeTypes = $jsonNode->attributeTypes();
		$dataType = empty($attributeTypes['data']) ? '' : $attributeTypes['data'];

		$jsonNodes = array();
		foreach ($nodeset as $node /* @var $node \BogoTree\Node */) {
//			print_r($node);
			$jsonNode = new static();

			$jsonNode->data = $dataType ? $dataType::createOne($node->getObject()) : $node->getObject();

			$childNodeset = $node->getChildren();
			if (count($childNodeset) > 0) {
				$jsonNode->children = static::createFromNodeset($childNodeset);
			}

			$jsonNodes[] = $jsonNode;
		}

		return $jsonNodes;
	}
}