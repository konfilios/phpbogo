<?php
/*
 */
namespace Bogo\Yii\Webservice\Dto;
use Bogo\Yii\Webservice;

/**
 *
 * @package web.models
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class IdTitle extends Webservice\Dto
{
	/**
	 * Id.
	 *
	 * @var mixed
	 */
	public $id;

	/**
	 * Title.
	 *
	 * @var string
	 */
	public $title;
}