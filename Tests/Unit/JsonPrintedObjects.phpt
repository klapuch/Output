<?php
declare(strict_types = 1);

/**
 * @testCase
 * @phpVersion > 7.2
 */

namespace Klapuch\Output\Unit;

use Klapuch\Output;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class JsonPrintedObjects extends Tester\TestCase {
	public function testMergingMultipleToPrettyArray(): void {
		Assert::same(
			'[{"a":"b"},{"c":"d"}]',
			(new Output\JsonPrintedObjects(
				static function (object $object, Output\Format $format): Output\Format {
					return $object->print($format);
				},
				new class {
					public function print(): Output\Format {
						return new Output\Json(['a' => 'b']);
					}
				},
				new class {
					public function print(): Output\Format {
						return new Output\Json(['c' => 'd']);
					}
				}
			))->serialization()
		);
	}

	public function testMergingSingleToPrettyArray(): void {
		Assert::same(
			'[{"a":"b"}]',
			(new Output\JsonPrintedObjects(
				static function (object $object, Output\Format $format): Output\Format {
					return $object->print($format);
				},
				new class {
					public function print(): Output\Format {
						return new Output\Json(['a' => 'b']);
					}
				}
			))->serialization()
		);
	}

	public function testAdjustingWithKeptOrder(): void {
		Assert::same(
			'[{"a":"B"},{"c":"D"}]',
			(new Output\JsonPrintedObjects(
				static function (object $object, Output\Format $format): Output\Format {
					return $object->print($format);
				},
				new class {
					public function print(): Output\Format {
						return new Output\Json(['a' => 'b']);
					}
				},
				new class {
					public function print(): Output\Format {
						return new Output\Json(['c' => 'd']);
					}
				}
			))->adjusted(null, static function (array $input): array {
				return array_map('strtoupper', $input);
			})->serialization()
		);
	}
}

(new JsonPrintedObjects())->run();
