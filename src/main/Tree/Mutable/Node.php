<?php
namespace Bogo\Tree\Mutable;

/**
 * Mutable Node.
 *
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Node extends \Bogo\Tree\Node
{
	/**
	 * Distance from the root.
	 *
	 * @var integer
	 */
	private $height;

	/**
	 * Wraps a piece of data in a tree node.
	 *
	 * @param mixed $object
	 * @param mixed $nodeId
	 * @param \Bogo\Tree\Mutable\Node $parentNode
	 */
	public function __construct($object, $nodeId, $parentNode = null)
	{
		parent::__construct($object, $nodeId);
		$this->setParentNode($parentNode);
	}

	/**
	 * Set new parent node.
	 *
	 * @param \Bogo\Tree\Mutable\Node $parentNode
	 */
	public function setParentNode($parentNode)
	{
		if ($parentNode === null) {
			$this->height = 0;
		} else {
			$this->parentNode = $parentNode;
			$this->height = $parentNode->getHeight() + 1;
		}
	}

	/**
	 * Add a child node.
	 *
	 * @param Node $child
	 */
	public function addChild($child)
	{
		$this->children[] = $child;
	}

	/**
	 * Distance from the root.
	 *
	 * @return integer
	 */
	public function getHeight()
	{
		return $this->height;
	}
}
