<?php
namespace Bogo\Tree\Mutable;

/**
 * Mutable tree.
 *
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Tree extends \Bogo\Tree\Tree
{
	/**
	 * Map of all nodes.
	 *
	 * @var \Bogo\Tree\Mutable\Node[]
	 */
	private $nodes = array();

	/**
	 * Map of orphan nodes.
	 *
	 * @var integer[][]
	 */
	private $orphanNodeIds = array();

	/**
	 * Create a new node.
	 *
	 * @param mixed $object
	 * @param mixed $nodeId
	 * @param mixed $parentNodeId
	 * @return \Bogo\Tree\Mutable\Node
	 */
	public function makeNode($object, $nodeId, $parentNodeId = null)
	{
		// Instantiate node
		$node = new \Bogo\Tree\Mutable\Node($object, $nodeId);

		// Save new node in full node array
		$this->nodes[$nodeId] = $node;

		if ($parentNodeId === null) {
			// Root node
			$this->rootNodes[$nodeId] = $node;

		} else {
			// Non-root node
			if (isset($this->nodes[$parentNodeId])) {
				// Normal non-root node
				$parentNode = $this->nodes[$parentNodeId];

				// Link parent to new node
				$node->setParentNode($parentNode);

				// Link new node to parent
				$parentNode->addChild($node);
			} else {
				// Orphan non-root node

				// Make it look like a root node
				$this->rootNodes[$nodeId] = $node;

				// Mark it as orphan in case our parent does show up
				$this->orphanNodeIds[$parentNodeId][] = $nodeId;
			}
		}

		// Is this the parent of nodes previously declared as orphan?
		if (isset($this->orphanNodeIds[$nodeId])) {
			// Claim our orphans!
			foreach ($this->orphanNodeIds[$nodeId] as $childNodeId) {
				$orphanChild = $this->nodes[$childNodeId];

				// Link parent to orphan
				$orphanChild->setParentNode($node);

				// Link orphan to parent
				$node->addChild($orphanChild);

				// Orphan node is not considered root any more
				unset($this->rootNodes[$childNodeId]);
			}

			// No orphans exist for this parent any more
			unset($this->orphanNodeIds[$nodeId]);
		}

		return $node;
	}

	/**
	 * Get a node by its id.
	 *
	 * @param mixed $nodeId
	 * @return \Bogo\Tree\Mutable\Node
	 */
	public function getNodeById($nodeId)
	{
		if (isset($this->nodes[$nodeId])) {
			return $this->nodes[$nodeId];
		} else {
			return null;
		}
	}

	/**
	 * Node array of nodes matching passed ids.
	 *
	 * @param integer[]|integer $nodeIds
	 * @return \Bogo\Tree\NodeArray
	 */
	public function getNodesetByIds($nodeIds)
	{
		if (!is_array($nodeIds)) {
			$nodeIds = array($nodeIds);
		}

		$nodeset = new \Bogo\Tree\NodeArray();
		foreach ($nodeIds as $nodeId) {
			if (isset($this->nodes[$nodeId])) {
				$nodeset[] = $this->nodes[$nodeId];
			}
		}

		return $nodeset;
	}
}
