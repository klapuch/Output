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

final class CombinedFormat extends Tester\TestCase {
	public function testSerializingAllPassedFormats() {
		Assert::same(
			'abcd',
			(new Output\CombinedFormat(
				new Output\FakeFormat('a'),
				new Output\FakeFormat('b'),
				new Output\FakeFormat('c'),
				new Output\FakeFormat('d')
			))->serialization()
		);
	}

	public function testAppendingUsingTheLastFormat() {
		Assert::same(
			'abcd|Ekey|e|',
			(new Output\CombinedFormat(
				new Output\ArrayFormat(['a']),
				new Output\ArrayFormat(['b']),
				new Output\ArrayFormat(['c']),
				new Output\FakeFormat('d')
			))->with('Ekey', 'e')->serialization()
		);
	}

	public function testAdjustingEveryMatchingKey() {
		Assert::same(
			'ABCDe',
			(new Output\CombinedFormat(
				new Output\ArrayFormat(['a']),
				new Output\ArrayFormat(['b']),
				new Output\ArrayFormat(['c']),
				new Output\ArrayFormat(['d']),
				new Output\ArrayFormat([1 => 'e'])
			))->adjusted(0, 'strtoupper')->serialization()
		);
	}
}

(new CombinedFormat())->run();