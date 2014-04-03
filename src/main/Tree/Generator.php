<?php
/*
 */

namespace Bogo\Tree;

/**
 * Description of Generator
 *
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Generator
{
	private $rootObjects = array();
	private $childObjectsPerId = array();

	public function getRootObjects()
	{
		return $this->rootObjects;
	}

	public function getChildObjectsById($id)
	{
		if (isset($this->childObjectsPerId[$id])) {
			return $this->childObjectsPerId[$id];
		} else {
			return array();
		}
	}

	public function addRootNode($object, $id)
	{
		$this->rootObjects[$id] = $object;
	}

	public function addNode($object, $id, $parentId)
	{
		$this->childObjectsPerId[$parentId][$id] = $object;
	}
}
