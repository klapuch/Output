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

final class Xml extends Tester\TestCase {
	public function testMultipleTypes(): void {
		Assert::same(
			'<root><price>400</price><type>useful</type></root>',
			(new Output\Xml(
				['price' => 400, 'type' => 'useful'],
				'root'
			))->serialization()
		);
	}

	public function testTextBoolean(): void {
		Assert::same(
			'<root><yes>true</yes><no>false</no><zero>0</zero><one>1</one></root>',
			(new Output\Xml(
				['yes' => true, 'no' => false, 'zero' => 0, 'one' => 1],
				'root'
			))->serialization()
		);
	}

	public function testEscapedCharacters(): void {
		Assert::same(
			'<root><escape>&lt;&gt;"&amp;\'</escape></root>',
			(new Output\Xml(
				['escape' => "<>\"&'"],
				'root'
			))->serialization()
		);
	}

	public function testUsingUt8Encoding(): void {
		Assert::same(
			'<root><encoding>Koňíček úpěl</encoding></root>',
			(new Output\Xml(
				['encoding' => 'Koňíček úpěl'],
				'root'
			))->serialization()
		);
	}

	public function testNestedChild(): void {
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
			))->serialization()
		);
	}

	public function testEmptyShortTag(): void {
		Assert::same('<root/>', (new Output\Xml([], 'root'))->serialization());
	}

	public function testAppendingToEmptyXml(): void {
		Assert::same(
			'<root><simple>SIMPLE</simple></root>',
			(new Output\Xml([], 'root'))->with('simple', 'SIMPLE')->serialization()
		);
	}

	public function testFirstlyStatedNodesWithPrecendence(): void {
		Assert::equal(
			(new Output\Xml(
				['name' => 'Dominik', 'id' => '5'],
				'root'
			))->serialization(),
			(new Output\Xml(['name' => 'Dominik'], 'root'))
				->with('id', '5')
				->with('name', 'foo')
				->serialization()
		);
	}

	public function testAdjustingUsingAnonymousFunction(): void {
		Assert::same(
			'<root><chars>FOO</chars><time>2015-01-01</time></root>',
			(new Output\Xml([], 'root'))
				->with('time', '2015-01-01')
				->with('chars', 'foo')
				->adjusted('chars', static function(string $chars) {
					return strtoupper($chars);
				})
			->serialization()
		);
	}

	public function testAdjustingUsingStringCallback(): void {
		Assert::same(
			'<root><chars>FOO</chars><time>2015-01-01</time></root>',
			(new Output\Xml([], 'root'))
				->with('time', '2015-01-01')
				->with('chars', 'foo')
				->adjusted('chars', 'strtoupper')
				->serialization()
		);
	}

	public function testAdjustingStatedValues(): void {
		Assert::same(
			'<root><chars>FOO</chars><time>2015-01-01</time></root>',
			(new Output\Xml(['chars' => 'foo'], 'root'))
				->with('time', '2015-01-01')
				->adjusted('chars', 'strtoupper')
				->serialization()
		);
	}

	public function testIgnoringUnknownTagToBeAdjusted(): void {
		Assert::same(
			'<root><chars>foo</chars></root>',
			(new Output\Xml(['chars' => 'foo'], 'root'))
				->adjusted('foo', 'strtoupper')
				->serialization()
		);
	}

	public function testAttribute(): void {
		Assert::same(
			'<root type="useful"><price>400</price></root>',
			(new Output\Xml(
				['price' => 400, '@type' => 'useful'],
				'root'
			))->serialization()
		);
	}

	/**
	 * @throws \DOMException Invalid Character Error
	 */
	public function testThrowingOnWrongAttribute(): void {
		Assert::same(
			'<root &amp;type="&amp;useful"><price>400</price></root>',
			(new Output\Xml(
				['price' => 400, '@&type' => '&useful'],
				'root'
			))->serialization()
		);
	}

	public function testMultipleAttributes(): void {
		Assert::same(
			'<root type="useful" name="me"><price>400</price></root>',
			(new Output\Xml(
				['price' => 400, '@type' => 'useful', '@name' => 'me'],
				'root'
			))->serialization()
		);
	}
}

(new Xml())->run();
