<?php
declare(strict_types = 1);

/**
 * @testCase
 * @phpVersion > 7.1
 */

namespace Klapuch\Output\Unit;

use Klapuch\Output;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class Json extends Tester\TestCase {
	public function testSerializingToPrettyJson(): void {
		Assert::same(
			'{
    "name": "Dom"
}',
			(new Output\Json(['name' => 'Dom']))->serialization()
		);
	}

	public function testAddingNewSimpleItem(): void {
		Assert::same(
			['title' => 'none', 'name' => 'Dom'],
			json_decode(
				(new Output\Json(['name' => 'Dom']))
					->with('title', 'none')
					->serialization(),
				true
			)
		);
	}

	public function testNewAddedItemWithPriority(): void {
		Assert::same(
			['name' => 'Dom'],
			json_decode(
				(new Output\Json(['name' => 'none']))
					->with('name', 'Dom')
					->serialization(),
				true
			)
		);
	}

	public function testAddingArray(): void {
		Assert::same(
			['properties' => ['age' => 21, 'eyes' => 'blue'], 'name' => 'Dom'],
			json_decode(
				(new Output\Json(['name' => 'Dom']))
					->with('properties', ['age' => 21, 'eyes' => 'blue'])
					->serialization(),
				true
			)
		);
	}

	public function testMergingFieldsWithSameName(): void {
		Assert::same(
			['properties' => ['age' => 22, 'skin' => 'white', 'eyes' => 'blue'], 'name' => 'Dom'],
			json_decode(
				(new Output\Json(['name' => 'Dom']))
					->with('properties', ['age' => 21, 'eyes' => 'blue'])
					->with('properties', ['age' => 22, 'skin' => 'white'])
					->serialization(),
				true
			)
		);
	}

	public function testAdjustingSingleItem(): void {
		Assert::same(
			['name' => 'Dom'],
			json_decode(
				(new Output\Json(['name' => 'Dom ']))
					->adjusted('name', 'trim')
					->serialization(),
				true
			)
		);
	}

	public function testAdjustingWhole(): void {
		Assert::same(
			['age' => 22],
			json_decode(
				(new Output\Json(['name' => 'Dom', 'age' => 21, 'nested' => ['foo' => 'bar', 'bar' => 'baz']]))
					->adjusted(null, static function (array $self) {
						return ['age' => $self['age'] + 1];
					})
					->serialization(),
				true
			)
		);
	}
}

(new Json())->run();
