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

final class FilledFormat extends Tester\TestCase {
	public function testSerializingSetUsingOrigin() {
		Assert::same(
			'|name|me||title|god|',
			(new Output\FilledFormat(
				new Output\FakeFormat(''),
				['name' => 'me', 'title' => 'god']
			))->serialization()
		);
	}

	public function testNullAsEmpty() {
		Assert::same(
			'<root><name>me</name><title></title></root>',
			(new Output\FilledFormat(
				new Output\Xml([], 'root'),
				['name' => 'me', 'title' => null]
			))->serialization()
		);
	}

	public function testAppendingToSetUsingOrigin() {
		Assert::same(
			'|name|me||title|god||age|20|',
			(new Output\FilledFormat(
				new Output\FakeFormat(''),
				['name' => 'me', 'title' => 'god']
			))->with('age', 20)->serialization()
		);
	}

	public function testAdjustUsingOrigin() {
		Assert::same(
			'MEgod',
			(new Output\FilledFormat(
				new Output\ArrayFormat([]),
				['name' => 'me', 'title' => 'god']
			))->adjusted('name', 'strtoupper')->serialization()
		);
	}
}

(new FilledFormat())->run();