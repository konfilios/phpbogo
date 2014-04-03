<?php
namespace Bogo\Tree;

/**
 * Abstract Tree
 *
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Tree implements INodeset
{
	/**
	 * Root nodes.
	 *
	 * @var NodeArray
	 */
	protected $rootNodes;

	public function __construct()
	{
		$this->rootNodes = new NodeArray();
	}

	public function getRootNodes()
	{
		return $this->rootNodes;
	}

	public function current()
	{
		return $this->rootNodes->current();
	}

	public function getChildren()
	{
		return $this->rootNodes->current()->getChildren();
	}

	public function hasChildren()
	{
//		print_r($this->rootNodes->current()->getId()); echo "\n";
//		print_r($this->rootNodes->current()->hasChildren()); echo "\n";
		return $this->rootNodes->current()->hasChildren();
	}

	public function key()
	{
		return $this->rootNodes->key();
	}

	public function next()
	{
		return $this->rootNodes->next();
	}

	public function rewind()
	{
		return $this->rootNodes->rewind();
	}

	public function valid()
	{
		return $this->rootNodes->valid();
	}

}
