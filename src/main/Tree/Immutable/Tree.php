<?php
namespace Bogo\Tree\Immutable;

/**
 * Immutable Tree
 *
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Tree extends \Bogo\Tree\Tree
{

	public function __construct(\Bogo\Tree\Generator $generator)
	{
		parent::__construct();

		foreach ($generator->getRootObjects() as $id=>$object) {
			$rootNode = new Node($object, $id, $generator, 0, null);
			$this->rootNodes[] = $rootNode;
		}
	}

}
