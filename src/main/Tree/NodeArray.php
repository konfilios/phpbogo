<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Bogo\Tree;

/**
 * Description of NodeArray
 *
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class NodeArray extends \RecursiveArrayIterator implements INodeset
{
	public function getChildren()
	{
		return $this->current()->getChildren();
	}

	public function hasChildren()
	{
		return $this->current()->hasChildren();
	}
}
