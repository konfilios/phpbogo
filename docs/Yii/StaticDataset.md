bogo-yii-static-dataset
=======================

Static dataset management for reference tables.

## Problem definition

Many of view may have often faced the following problem:

1.  You have databases tables in which **some fields are enumerable**. Examples are `status`, `state`, `mode`, `action`, `type`, etc.
2.  You need to **hardcode** the different applicable values as constants in your application so you write custom functionality
   for each one of them. E.g. if `Product.status` equals 1 then do this, or if `Menu.action` equals 2 do that, etc.
3.  You need to maintain **reference tables** containing a single record for each of these enumerated values, most commonly
   linked through foreign keys with your business table.
   Good reasons for this is either you want to have a self-explicable database for your db team or either enforce data integrity. E.g. you have
   a reference table which lists all possible values of `Product.status`, etc.
4.  You have **multiple database instances** to keep in sync. E.g. per developer, staging, pre-production, production, etc.


## Example

### Developer-friendly version

#### Database schema definition

Let's suppose you have a `ShopOrder` table with the following definition in a `ShopMigration` class
which you can instantiate anytime to easily re-create a full instance of your database schema in
different environments (development, staging, production, etc).

```php
class ShopMigration extends CDbMigration
{
	public function createSchema()
	{
		// [..]
		// Create `User` table
		// [..]

		// Create `ShopOrder` table
		$this->createTable('ShopOrder', array(
			'id' => 'pk',								// Primary key
			'userId' => 'integer NOT NULL',				// Owner user
			'status' => 'integer NOT NULL',				// Current order status
			'createDatetime' => 'datetime NOT NULL',	// Creation datetime
		));

		// Add foreign key to `userId`
		// [..]
	}
}
```

#### Yii active record model definition

Here's what your model class would look like, with the different `status` values defined
as constants, so they can be used in business-logic code (assuming you use constants and you
are not a fan of the famous [http://en.wikipedia.org/wiki/Magic_number_%28programming%29]("magic numbers") anti-pattern.

```php
/**
 * Shop orders.
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $status
 * @property string $createDatetime
 *
 * @property User $user
 */
class ShopOrder extends CActiveRecord
{
	const STATUS_OPEN = 1;			// Open order, items are still added/removed
	const STATUS_SUBMITTED = 2;		// Submitted for processing, no changes are allowed
	const STATUS_PROCESSED = 3;		// Processing finished successfully

	static public $statusTitles = array(
		self::STATUS_OPEN = "Open",
		self::STATUS_SUBMITTED = "Submitted",
		self::STATUS_PROCESSED = "Processed",
	);

//	[..]
	public function relations()
	{
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'userId'),
		);
	}
//	[..]

	public function getStatusTitle()
	{
		return isset(self::$statusTitles[$this->status]) ? self::$statusTitles[$this->status] : null;
	}
//	[..]
}
```

#### The `status` drop-down box

Notice that there's also a static public array called `$statusTitles` which contains human-readable
titles for the status values. This will probably be useful in some administration panel where
the admin will want to filter or report orders based on a given status. This array will be used
to populate the dropdown menu.

This is what you would type in your view, assuming you want a `filterStatus` filter which
is assigned to `$filterStatus` variable.

```php
echo CHtml::dropDownList('filterStatus', $filterStatus, ShopOrder::$statusTitles);
```

#### Adding a new status

Let's, now, suppose that you realized you're missing a critical status, the **rejected** one, which
appears if an order is rejected after it's submitted. All you have to do is the following:
1.  Add a new constant in the `ShopOrder` class (nameley `STATUS_REJECTED = 4`)
2.  Add a new row in the `$statusTitles` variable

This brings us to the following state of the `ShopOrder` class (without the phpdoc comments):

```php
class ShopOrder extends CActiveRecord
{
//	[..]
	const STATUS_OPEN = 1;			// Open order, items are still added/removed
	const STATUS_SUBMITTED = 2;		// Submitted for processing, no changes are allowed
	const STATUS_PROCESSED = 3;		// Processing finished successfully
	const STATUS_REJECTED = 4;		// Rejected while processing

	static public $statusTitles = array(
		self::STATUS_OPEN = "Open",
		self::STATUS_SUBMITTED = "Submitted",
		self::STATUS_PROCESSED = "Processed",
		self::STATUS_REJECTED = "Rejected",
	);
//	[..]
}
```


### A more BI-friendly version

Business-Intelligence people, database admins, etc. like well-documented database schemata. Keeping
your schema properly documented (with comments on the tables, the fields, etc) is usually a
tedious task and people tend to skip it, so everybody hopes that at least the names you choose
for your database objects are pretty much straight forward and descriptive of what they mean and do.

No matter how well you name a table or column, the database guy browsing your table will probably
never figure out what's the meaning of `status=1`. And the only answer is either in some **documentation
file** (not really common) or **in your code** (hopefully a constant in your model definition).

Another approache is using **reference tables** and keeping one record for each one of your
"magic" numbers. Ideally this reference table should be linked with your initial business table
using a foreign key.

#### Database schema definition

Below is the schema definition with the new reference table for `status`.

```php
class ShopMigration extends CDbMigration
{
	public function createSchema()
	{
		// [..]
		// Create `User` table
		// [..]

		// Create `ShopOrder` table
		$this->createTable('ShopOrderStatus', array(
			'id' => 'pk*',								// Primary key (not auto-increment)
			'title' => 'string NOT NULL',				// Human-readable title
			'notes' => 'string NOT NULL',				// An internal note for db admin
		));

		// Create `ShopOrder` table
		$this->createTable('ShopOrder', array(
			'id' => 'pk',								// Primary key
			'userId' => 'integer NOT NULL',				// Owner user
			'statusId' => 'integer NOT NULL',			// Current order status
			'createDatetime' => 'datetime NOT NULL',	// Creation datetime
		));

		// Add foreign key to `userId`
		// [..]
	}

	public function populateReferenceDataset()
	{
		$statuses = array(
			array(
				'id' => ShopOrderStatus::ID_OPEN,
				'title' => 'Open',
				'notes' => 'Open order, items are still added/removed',
			),
			array(
				'id' => ShopOrderStatus::ID_SUBMITTED,
				'title' => 'Submitted',
				'notes' => 'Submitted for processing, no changes are allowed',
			),
			array(
				'id' => ShopOrderStatus::ID_PROCESSED,
				'title' => 'Processed',
				'notes' => 'Processing finished successfully',
			),
		);

		foreach ($statuses as $statusAttributes) {
			// Check if currently examined status is already in the database
			$statusModel = ShopOrderStatus::model()->findByAttributes(array('id'=>$statusAttributes['id']));

			if ($statusModel === null) {
				// Not found, create a new instance for insertion
				$statusModel = new ShopOrderStatus();
			}

			// Set/update all attributes
			$statusModel->attributes = $statusAttributes;

			// Update/insert in database
			$statusModel->save();
		}
	}
}
```

There are a few things worth noticing here:
1.  The new `ShopOrderStatus` table has a **primary key which is not auto-incremented**. This is
   because you want to assign numbers yourself and use them consistently both in the code and
   the database.
2.  The `ShopeOrder.status` column was renamed to `ShopOrder.statusId`. The `Id` postfix is there
   to signify a foreign key.
3.  The `ShopOrderStatus.id` column (and, thus, the `ShopOrder.statusId` as well) could of course
   be `tinyint` for storage optimization, but let's not stick to that for now.
4.  The new `populateReferenceDataset` method which maintains one record per status value.
5.  The new `populateReferenceDataset` method is [http://en.wikipedia.org/wiki/Idempotence](idepontent), i.e.
   it does not (and should not) matter how many times we execute it on any database instance. If
   a record is already there, it doesn't insert it again. It just updates it to enforce synchronization
   between what's in our code and what's in our reference table.
6.  The static `$statusTitles` variable was moved from `ShopOrder` model to the migration code and
   reformatted to keep the related notes (previously only defined as inline comments) as well.

#### Yii active record model definition

In our new scenario, an extra model is needed, the one representing the new table `ShopOrderStatus`. Of course
it must also be linked with the `ShopOrder` model using the `relations()` method.


```php
/**
 * Shop orders status values.
 *
 * @property integer $id
 * @property string $title
 * @property string $notes
 */
class ShopOrderStatus extends CActiveRecord
{
//	[..]

	const ID_OPEN = 1;			// Open order, items are still added/removed
	const ID_SUBMITTED = 2;		// Submitted for processing, no changes are allowed
	const ID_PROCESSED = 3;		// Processed, final state

//	[..]
}
```


```php
/**
 * Shop orders.
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $statusId
 * @property string $createDatetime
 *
 * @property User $user
 * @property ShopOrderStatus $status
 */
class ShopOrder extends CActiveRecord
{

//	[..]
	public function relations()
	{
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'userId'),
			'status' => array(self::BELONGS_TO, 'ShopOrderStatus', 'statusId'),
		);
	}
//	[..]
}
```

Notice that the `STATUS_` constants were relocated from the `ShopOrder` model as `ID_` constants
in the `ShopOrderStatus` model.

Getting rid of the constants is not really possible, or the subject of this discussion after all.

#### The `status` drop-down box

The list of status values can now be retrieved from the database instead of a static variable:

```php
echo CHtml::dropDownList('filterStatusId', $filterStatusId, CHtml::listData(ShopOrderStatus::model()->findAll(), 'id', 'title'));
```

#### Adding a new status

It's now time to deal with the problem exhibited above, i.e. add the new `rejected` status. The
procedure is pretty similar:
1.  First of all we add a new constant in the `ShopOrderStatus` class, namely `ID_REJECTED = 4`
2.  We add a new entry in the array found in the `populateReferenceDataset` method of our
   migration class
3.  We run the migration method on all instances to make sure they're synchronized.


```php
class ShopMigration extends CDbMigration
{
	// [..]

	public function populateReferenceDataset()
	{
		$statuses = array(
			array(
				'id' => ShopOrderStatus::ID_OPEN,
				'title' => 'Open',
				'notes' => 'Open order, items are still added/removed',
			),
			array(
				'id' => ShopOrderStatus::ID_SUBMITTED,
				'title' => 'Submitted',
				'notes' => 'Submitted for processing, no changes are allowed',
			),
			array(
				'id' => ShopOrderStatus::ID_PROCESSED,
				'title' => 'Processed',
				'notes' => 'Processing finished successfully',
			),
			array(
				'id' => ShopOrderStatus::ID_REJECTED,
				'title' => 'Rejected',
				'notes' => 'Rejected while processing',
			),
		);
		// [..]
	}
}
```
