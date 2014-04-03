<?php
namespace Bogo\Tree;

/**
 * Tree node.
 *
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Node implements INodeset
{
	/**
	 * Node id.
	 *
	 * @var mixed
	 */
	protected $id;

	/**
	 * Object wrapped in node.
	 *
	 * @var mixed
	 */
	protected $object;

	/**
	 * Parent node.
	 *
	 * @var \Bogo\Tree\Node
	 */
	protected $parentNode;

	/**
	 * Child nodes.
	 *
	 * @var \Bogo\Tree\NodeArray
	 */
	protected $children = null;

	/**
	 * Wrap given object in a node with given id.
	 *
	 * @param mixed $object
	 * @param mixed $nodeId
	 */
	public function __construct($object, $nodeId)
	{
		$this->i = 0;
		$this->id = $nodeId;
		$this->object = $object;
		$this->children = new NodeArray();
	}

	/**
	 * Node id.
	 *
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Object wrapped in node.
	 *
	 * @return mixed
	 */
	public function getObject()
	{
		return $this->object;
	}

	/**
	 * Is this a leaf node?
	 *
	 * @return boolean
	 */
	public function isLeaf()
	{
		return !empty($this->children);
	}

	/**
	 * Is this a root node?
	 *
	 * @return boolean
	 */
	public function isRoot()
	{
		return ($this->parentNode === null);
	}

	/**
	 * Child nodes.
	 *
	 * @return \Bogo\Tree\NodeArray
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * Pseudo-counter for iterations.
	 *
	 * @var integer
	 */
	private $i;

	public function current()
	{
		return $this;
	}

	public function hasChildren()
	{
		return !empty($this->children);
	}

	public function key()
	{
		return $this->i;
	}

	public function next()
	{
		$this->i++;
	}

	public function rewind()
	{
		$this->i = 0;
	}

	public function valid()
	{
		return ($this->i <= 1);
	}
}
