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

final class Xml extends Tester\TestCase {
	public function testBasicXmFormat() {
		Assert::same(
			'<root><price>400</price><type>useful</type><escape>&lt;&gt;&quot;&amp;&apos;</escape></root>',
			(new Output\Xml(
				['price' => 400, 'type' => 'useful', 'escape' => "<>\"&'"],
				'root'
			))->serialize()
		);
	}

	public function testNestedArrays() {
		Assert::same(
			'<root><price>400</price><type>useful</type><lines><id>123</id><name>ABC</name></lines></root>',
			(new Output\Xml(
				[
					'price' => 400,
					'type' => 'useful',
					'lines' => [
						[
							'id' => 123,
							'name' => 'ABC',
						],
					],
				],
				'root'
			))->serialize()
		);
	}

	public function testEmptyInputWithoutFail() {
		Assert::same('<root></root>', (new Output\Xml([], 'root'))->serialize());
	}

	public function testAppendingToEmptyXml() {
		Assert::same(
			'<root><simple>SIMPLE</simple></root>',
			(new Output\Xml([], 'root'))->with('simple', 'SIMPLE')->serialize()
		);
	}

	public function testAddingNodesWithoutOverwriting() {
		Assert::equal(
			(new Output\Xml(
				['name' => 'Dominik', 'id' => '5'],
				'root'
			))->serialize(),
			(new Output\Xml(['name' => 'Dominik'], 'root'))
			->with('id', '5')
			->with('name', 'foo')
			->serialize()
		);
	}

	public function testAddingNodesWithoutContent() {
		Assert::same(
			'<root><AAA><XXX><name>Dominik</name></XXX></AAA></root>',
			(new Output\Xml(['name' => 'Dominik'], 'root'))
			->with('XXX')
			->with('AAA')
			->serialize()
		);
	}

	public function testAddingArrayNodes() {
		Assert::same(
			'<root><OUTER><name>Dominik</name><XXX><xxx_inner><who>xxx</who></xxx_inner></XXX><INNER><who>me</who></INNER></OUTER></root>',
			(new Output\Xml(['name' => 'Dominik'], 'root'))
			->with('XXX', ['xxx_inner' => ['who' => 'xxx']])
			->with('INNER', ['who' => 'me'])
			->with('OUTER')
			->serialize()
		);
	}
}

(new Xml())->run();