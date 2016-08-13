<?php
/**
 * @testCase
 * @phpVersion > 5.6
 */
namespace Klapuch\Unit\Output;

use Klapuch\Output;
use Tester;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

final class XmlPrinter extends Tester\TestCase {
	public function testCorrectlyXmlPrinterFormat() {
		Assert::same(
			'<root><price>400</price><type>useful</type><escape>&lt;&gt;"&amp;\'</escape></root>',
			(string)new Output\XmlPrinter(
				'root',
				['price' => 400, 'type' => 'useful', 'escape' => "<>\"&'"]
			)
		);
	}

	public function testXmlPrinterFormatWithNestedArrays() {
		Assert::same(
			'<root><price>400</price><type>useful</type><lines><id>123</id><name>ABC</name></lines></root>',
			(string)new Output\XmlPrinter(
				'root',
				[
					'price' => 400,
					'type' => 'useful',
					'lines' => [
						[
							'id' => 123,
							'name' => 'ABC',
						],
					],
				]
			)
		);
	}

	public function testEmptyOutputWithoutError() {
		Assert::same('<root></root>', (string)new Output\XmlPrinter('root', []));
	}

	public function testAddingWithoutOverwriting() {
		Assert::equal(
			new Output\XmlPrinter(
				'root',
				['name' => 'Dominik', 'id' => '5']
			),
			(new Output\XmlPrinter('root', ['name' => 'Dominik']))
				->with('id', '5')
				->with('name', 'foo')
		);
	}

	public function testAddingEmptyNodes() {
		Assert::same(
			'<root><AAA><XXX><name>Dominik</name></XXX></AAA></root>',
			(string)(new Output\XmlPrinter('root', ['name' => 'Dominik']))
				->with('XXX')
				->with('AAA')
		);
	}

	public function testAddingArrayNodes() {
		Assert::same(
			'<root><OUTER><name>Dominik</name><XXX><xxx_inner><who>xxx</who></xxx_inner></XXX><INNER><who>me</who></INNER></OUTER></root>',
			(string)(new Output\XmlPrinter('root', ['name' => 'Dominik']))
				->with('XXX', ['xxx_inner' => ['who' => 'xxx']])
				->with('INNER', ['who' => 'me'])
				->with('OUTER')
		);
	}
}

(new XmlPrinter())->run();
