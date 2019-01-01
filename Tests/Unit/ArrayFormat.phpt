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

final class ArrayFormat extends Tester\TestCase {
	public function testSerializingArrayValues(): void {
		Assert::same(
			'abcd',
			(new Output\ArrayFormat(['a', 'b', 'c', 'd']))->serialization()
		);
	}

	public function testAdjustingByExistingKey(): void {
		Assert::same(
			'ABcd',
			(new Output\ArrayFormat(['a' => 'ab', 'b' => 'cd']))
				->adjusted('a', 'strtoupper')
				->serialization()
		);
	}

	public function testAdjustingByExistingKeyWithEmptyValue(): void {
		Assert::same(
			'abFOO',
			(new Output\ArrayFormat(['a' => 'ab', 'b' => null]))
				->adjusted('b', static function($null) {
					return 'FOO';
				})
			->serialization()
		);
	}

	public function testIgnoringUnknownKeyToBeAdjusted(): void {
		Assert::same(
			'abcd',
			(new Output\ArrayFormat(['a' => 'ab', 'b' => 'cd']))
				->adjusted('foo', 'strtoupper')
				->serialization()
		);
	}

	public function testAdjustingWithoutCreatingNewKey(): void {
		Assert::same(
			'abcd',
			(new Output\ArrayFormat(['a' => 'ab', 'b' => 'cd']))
				->adjusted('foo', 'strtoupper')
				->adjusted('foo', static function($foo) {
					return 'FOOOOOOOOO';
				})
			->serialization()
		);
	}

	public function testAppendingBrandNew(): void {
		Assert::same(
			'abcd',
			(new Output\ArrayFormat(['a' => 'ab']))
				->with('b', 'cd')
				->serialization()
		);
	}

	public function testAppendingWithoutOverwriting(): void {
		Assert::same(
			'abcd',
			(new Output\ArrayFormat(['a' => 'ab', 'b' => 'cd']))
				->with('b', 'foo')
				->serialization()
		);
	}
}

(new ArrayFormat())->run();
