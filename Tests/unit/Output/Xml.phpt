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
	public function testCorrectXmlFormat() {
		Assert::same(
			'<root><price>400</price><type>useful</type><escape>&lt;&gt;&quot;&amp;&apos;</escape></root>',
			(string)new Output\Xml(
				['price' => 400, 'type' => 'useful', 'escape' => "<>\"&'"],
				'root'
			)
		);
	}

	public function testXmlFormatWithNestedArrays() {
		Assert::same(
			'<root><price>400</price><type>useful</type><lines><id>123</id><name>ABC</name></lines></root>',
			(string)new Output\Xml(
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
			)
		);
	}

	public function testEmptyOutputWithoutError() {
		Assert::same('<root></root>', (string)new Output\Xml([], 'root'));
	}

	public function testAddingWithoutOverwriting() {
		Assert::equal(
			new Output\Xml(
				['name' => 'Dominik', 'id' => '5'],
				'root'
			),
			(new Output\Xml(['name' => 'Dominik'], 'root'))
				->with('id', '5')
				->with('name', 'foo')
		);
	}

	public function testAddingEmptyNodes() {
		Assert::same(
			'<root><AAA><XXX><name>Dominik</name></XXX></AAA></root>',
			(string)(new Output\Xml(['name' => 'Dominik'], 'root'))
				->with('XXX')
				->with('AAA')
		);
	}

	public function testAddingArrayNodes() {
		Assert::same(
			'<root><OUTER><name>Dominik</name><XXX><xxx_inner><who>xxx</who></xxx_inner></XXX><INNER><who>me</who></INNER></OUTER></root>',
			(string)(new Output\Xml(['name' => 'Dominik'], 'root'))
				->with('XXX', ['xxx_inner' => ['who' => 'xxx']])
				->with('INNER', ['who' => 'me'])
				->with('OUTER')
		);
	}
}

(new Xml())->run();
