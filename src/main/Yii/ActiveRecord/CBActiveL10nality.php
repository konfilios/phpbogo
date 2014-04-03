<?php
/**
 * L10n Active Record behavior.
 *
 * @since 1.3
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBActiveL10nality extends CBActiveIndexedRelationality
{
	/**
	 * Column name of related model used for indexing.
	 *
	 * @var string
	 */
	public $indexFkName = 'languageId';
	/**
	 * Relation name.
	 *
	 * @var string
	 */
	public $relationName = 'l10ns';

	/**
	 * Name of session relation.
	 *
	 * @var string
	 */
	public $sessionRelationName = 'sessionL10n';
}