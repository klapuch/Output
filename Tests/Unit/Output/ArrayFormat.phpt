<?php
/**
 * @testCase
 * @phpVersion > 7.0
 */
namespace Klapuch\Unit\Output;

use Klapuch\Output;
use Tester;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

final class ArrayFormat extends Tester\TestCase {
	public function testSerializingArrayValues() {
		Assert::same(
			'abcd',
			(new Output\ArrayFormat(['a', 'b', 'c', 'd']))->serialization()
		);
	}

	public function testAdjustingByExistingKey() {
		Assert::same(
			'ABcd',
			(new Output\ArrayFormat(['a' => 'ab', 'b' => 'cd']))
			->adjusted('a', 'strtoupper')
			->serialization()
		);
	}

	public function testAdjustingByExistingKeyWithEmptyValue() {
		Assert::same(
			'abFOO',
			(new Output\ArrayFormat(['a' => 'ab', 'b' => null]))
			->adjusted('b', function($null) { return 'FOO'; })
			->serialization()
		);
	}

	public function testIgnoringUnknownKeyToBeAdjusted() {
		Assert::same(
			'abcd',
			(new Output\ArrayFormat(['a' => 'ab', 'b' => 'cd']))
			->adjusted('foo', 'strtoupper')
			->serialization()
		);
	}

	public function testAdjustingWithoutCreatingNewKey() {
		Assert::same(
			'abcd',
			(new Output\ArrayFormat(['a' => 'ab', 'b' => 'cd']))
			->adjusted('foo', 'strtoupper')
			->adjusted('foo', function($foo) { return 'FOOOOOOOOO'; })
			->serialization()
		);
	}

	public function testAppendingBrandNew() {
		Assert::same(
			'abcd',
			(new Output\ArrayFormat(['a' => 'ab']))
			->with('b', 'cd')
			->serialization()
		);
	}

	public function testAppendingWithoutOverwriting() {
		Assert::same(
			'abcd',
			(new Output\ArrayFormat(['a' => 'ab', 'b' => 'cd']))
			->with('b', 'foo')
			->serialization()
		);
	}
}

(new ArrayFormat())->run();