bogo-dataset-iterators
======================

## Problem Statement

Suppose you have retrieved a **sorted list** of records from your database and want to display them
to your user in a **multi-column table* preserving a critical property:
The user should be able to read the data vertically sorted, i.e. scan the first column, which contains
the first ordered subset of your data, then skip to the second column, etc.. Exactly like an telephone
catalog works.

This can be too frustrating to code in a web application, since **an HTML table is printed/constructed
row-by-row, not column-by-column**.

The classes of this package hide all the tricky calculations from you and provide a pair of
nested iterators which is the natural choice when printing HTML tables: Outer iterator for the rows
and inner for the cells in that row.

## Installation

Extract the `lib/` folder under your project and make sure your autoloader can access the class
and interface files. The convention is simple and compatible with Yii framework:
Every class is stored in a file with the same name, followed by the standard `.php` extension.

Here's a sample autoloader.

```php
spl_autoload_register(function ($class) {
    include './lib/'.$class. '.php';
});
```

## A demo table construction function

Throughout the following examples, we will be outputting HTML tables. Your application will
probably have different requirements, but the simple function listed below is more than enough
to help you understand concepts and methods of this package:

```php

function printHtmlTable($iterator)
{
	echo("<table border=\"1\">\n");

	foreach ($iterator as $row) {
		echo("<tr>\n");

		foreach ($row as $key=>$item) {
			echo("<td>".(($item === null) ? "null" : "$key => $item")."</td>\n");
		}

		echo("</tr>\n");
	}

	echo("<table>\n");
}
```

* The `$iterator` variable is the one we're interested in.
* Valid items are printed in the form `key => value`.
* `null` denotes the absence of a valid element in your dataset to fill this cell. It's
up to you to fill it with a dash, a `&nbsp` or whatever you find appropriate.


## Usage Example

Here's an example of printing an 8-item array in a vertically-ordered 3-column html:

```php

$columnCount = 3;

$dataset = array(
	'Value 0',
	'Value 1',
	'Value 2',
	'Value 3',
	'Value 4',
	'Value 5',
	'Value 6',
	'Value 7',
);

printHtmlTable(new CBFixedColumnGrid(new CBIndexedArray($dataset), $columnCount));
```

This produces the following output:

| Col 1        | Col 2        | Col 3        |
| ------------ | ------------ | ------------ |
| 0 => Value 0 | 3 => Value 3 | 6 => Value 6 |
| 1 => Value 1 | 4 => Value 4 | 7 => Value 7 |
| 2 => Value 2 | 5 => Value 5 | null         |


## Indexed datasets

All calculations in the iterator implementation require that your initial dataset allows access
to their items using z**ero-based indices**. While this is true for simple php arrays, as the one
demonstrated above, it is not for php **associative** arrays.

This is why it's necessary to wrap your data in an appropriate class, either `CBIndexedArray` (for
simple arrays) or `CBIndexAssoc` (for associative arrays).

Here's a variation of the above example using non-integer keys as a dataset and `CBIndexedAssoc`
to wrap it:

```php

$columnCount = 3;

$dataset = array(
	'key0' => 'Value 0',
	'key1' => 'Value 1',
	'key2' => 'Value 2',
	'key3' => 'Value 3',
	'key4' => 'Value 4',
	'key5' => 'Value 5',
	'key6' => 'Value 6',
	'key7' => 'Value 7',
);

printHtmlTable(new CBFixedColumnGrid(new CBIndexedAssoc($dataset), $columnCount));
```

Here's the new output in the same `key => value` format:

| Col 1           | Col 2           | Col 3        |
| --------------- | --------------- | --------------- |
| key0 => Value 0 | key3 => Value 3 | key6 => Value 6 |
| key1 => Value 1 | key4 => Value 4 | key7 => Value 7 |
| key2 => Value 2 | key5 => Value 5 | null            |


## Fixed and Maximum Column Grids

There are certain combinations of dataset size and grid column counts which yield tables with
totally empty columns. Depending on your UI you might want to display these empty columns (for
example, if you want to preserve alignment with other tables in the same page, etc.) or prefer
to minimize the finally used columns to the minimum required.

Let's come back to the above example and see this problem in action by requesting 4 columns
instead of 3 and adding an element to our dataset.

```php

$columnCount = 4;

$dataset = array(
	'Value 0',
	'Value 1',
	'Value 2',
	'Value 3',
	'Value 4',
	'Value 5',
	'Value 6',
	'Value 7',
	'Value 8',
);

printHtmlTable(new CBFixedColumnGrid(new CBIndexedArray($dataset), $columnCount));
```

The output you get is the following:

| Col 1        | Col 2        | Col 3        | Col 4        |
| ------------ | ------------ | ------------ | ------------ |
| 0 => Value 0 | 3 => Value 3 | 6 => Value 6 | null         |
| 1 => Value 1 | 4 => Value 4 | 7 => Value 7 | null         |
| 2 => Value 2 | 5 => Value 5 | 8 => Value 8 | null         |

Yes, it seems weird, but you can confirm the result by executing the algorithm yourself on a paper.

Now, in case you wish to keep this empty column, you are free to do so. In case you want to skip
it, you may replace the `CBFixedColumnGrid` with a `CBMaxColumnGrid`.

A `CBMaxColumnGrid` accepts your wrapped dataset, as well as the number of columns you want, but
calculates how many columns you really need. In case you need the final number of rows determined
by the `CBMaxColumnGrid` (for example, if you want to use it an a `colspan` attribute, etc), then
you may call its `getColumnCount()` method.

Here's your new output:

| Col 1        | Col 2        | Col 3        |
| ------------ | ------------ | ------------ |
| 0 => Value 0 | 3 => Value 3 | 6 => Value 6 |
| 1 => Value 1 | 4 => Value 4 | 7 => Value 7 |
| 2 => Value 2 | 5 => Value 5 | 8 => Value 8 |

## Handy methods

Chaining object constructions in the code might feel strange to some, so there are 4 static factory
methods that correspond to the 4 combinations of `array`/`assoc` datasets vs `fixed`/`max` grids.

This is how your code will look like if you use these methods:

```php

$columnCount = 4;

//
// Array dataset
//
$dataset = array(
	'Value 0',
	'Value 1',
);

// Fixed column
printHtmlTable(CBFixedColumnGrid::fromArray($dataset, $columnCount));

// Max column
printHtmlTable(CBMaxColumnGrid::fromArray($dataset, $columnCount));

//
// Assoc dataset
//
$dataset = array(
	'key0' => 'Value 0',
	'key1' => 'Value 1',
);


// Fixed column
printHtmlTable(CBFixedColumnGrid::fromAssoc($dataset, $columnCount));

// Max column
printHtmlTable(CBMaxColumnGrid::fromAssoc($dataset, $columnCount));
```

The output of the above code is listed below.

### `CBFixedColumnGrid::fromArray`

| Col 1        | Col 2        | Col 3        | Col 4        |
| ------------ | ------------ | ------------ | ------------ |
| 0 => Value 0 | 1 => Value 1 | null         | null         |

### `CBMaxColumnGrid::fromArray`

| Col 1        | Col 2        |
| ------------ | ------------ |
| 0 => Value 0 | 1 => Value 1 |

### `CBFixedColumnGrid::fromAssoc`

| Col 1           | Col 2           | Col 3        | Col 4        |
| --------------- | --------------- | ------------ | ------------ |
| key0 => Value 0 | key1 => Value 1 | null         | null         |

### `CBMaxColumnGrid::fromAssoc`

| Col 1           | Col 2           |
| --------------- | --------------- |
| key0 => Value 0 | key1 => Value 1 |

