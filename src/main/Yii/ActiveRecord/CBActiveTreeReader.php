<?php
/**
 * CBActiveTreeReader.
 *
 * @since 1.3
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBActiveTreeReader
{
	/**
	 * Model finder.
	 *
	 * @var CActiveRecord
	 */
	private $modelFinder;

	/**
	 * Id column.
	 *
	 * @var string
	 */
	private $idColumn;

	/**
	 * Parent id column.
	 *
	 * @var string
	 */
	private $parentIdColumn;

	/**
	 * Result tree.
	 *
	 * @var \BogoTree\Mutable\Tree
	 */
	private $resultTree;

	/**
	 * Initialize tree reader.
	 *
	 * @param CActiveRecord $modelFinder
	 * @param string $idColumn
	 * @param string $parentIdColumn
	 */
	public function __construct($modelFinder, $idColumn, $parentIdColumn)
	{
		$this->modelFinder = $modelFinder;
		$this->idColumn = $idColumn;
		$this->parentIdColumn = $parentIdColumn;
	}

	/**
	 * Get full tree.
	 *
	 * @return \BogoTree\Mutable\Tree
	 */
	public function getFullTree()
	{
		$this->resultTree = new \BogoTree\Mutable\Tree();

		$this->readDownToLeaves(array(null));

		return $this->resultTree;
	}

	/**
	 * Get subtree of given model.
	 *
	 * @return \BogoTree\Mutable\Tree
	 */
	public function getSubtreeOf($rootModel)
	{
		$this->resultTree = new \BogoTree\Mutable\Tree();
//		$this->tree = $this->makeRoot($rootModel->{$this->parentIdColumn});

		$rootNodeId = $rootModel->{$this->idColumn};

		$this->resultTree->makeNode($rootModel, $rootNodeId);

		$this->readDownToLeaves(array($rootNodeId));

		return $this->resultTree;
	}

	/**
	 * Recursively read nodes down to the leaf level.
	 *
	 * @param integer[] $parentIds
	 */
	private function readDownToLeaves($parentIds)
	{
		$this->modelFinder->dbCriteria->addInCondition($this->parentIdColumn, $parentIds);

		$foundModels = $this->modelFinder->findAll();

		$readNodeIds = array();
		foreach ($foundModels as $foundModel) {
			$nodeId = $foundModel->{$this->idColumn};
			$parentNodeId = $foundModel->{$this->parentIdColumn};

			$this->resultTree->makeNode($foundModel, $nodeId, $parentNodeId ?: null);

			$readNodeIds[] = $nodeId;
		}

		if (!empty($readNodeIds)) {
			$this->readDownToLeaves($readNodeIds);
		}
	}

	/**
	 * Get full tree.
	 *
	 * @param integer $seedNodeId
	 * @return \BogoTree\Mutable\Tree
	 */
	public function getParentTreeOf($seedNodeId)
	{
		$this->resultTree = new \BogoTree\Mutable\Tree();

		$this->readUpToRoot($seedNodeId);

		return $this->resultTree;
	}

	/**
	 *
	 * @param integer $pivotModel
	 */
	private function readUpToRoot($pivotNodeId)
	{
		if (!empty($pivotNodeId)) {
			// Recurse up to the parent
			$pivotModel = $this->modelFinder->findByPk($pivotNodeId);

			$this->readUpToRoot($pivotModel->{$this->parentIdColumn});
		}

		// Find children of seed node
		$this->modelFinder->dbCriteria->addInCondition($this->parentIdColumn, array($pivotNodeId ?: null));

		$foundModels = $this->modelFinder->findAll();

		foreach ($foundModels as $foundModel) {
			$nodeId = $foundModel->{$this->idColumn};
			$parentNodeId = $foundModel->{$this->parentIdColumn};

			$this->resultTree->makeNode($foundModel, $nodeId, $parentNodeId ?: null);
		}
	}
}
