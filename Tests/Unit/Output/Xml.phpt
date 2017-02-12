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
	public function testMultipleTypes() {
		Assert::same(
			'<root><price>400</price><type>useful</type></root>',
			(new Output\Xml(
				['price' => 400, 'type' => 'useful'],
				'root'
			))->serialization()
		);
	}

	public function testEscapedCharacters() {
		Assert::same(
			'<root><escape>&lt;&gt;&quot;&amp;&apos;</escape></root>',
			(new Output\Xml(
				['escape' => "<>\"&'"],
				'root'
			))->serialization()
		);
	}

	public function testUsingUt8Encoding() {
		Assert::same(
			'<root><encoding>Koňíček úpěl</encoding></root>',
			(new Output\Xml(
				['encoding' => 'Koňíček úpěl'],
				'root'
			))->serialization()
		);
	}

	public function testNestedParents() {
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

	public function testNoEmptyShortTag() {
		Assert::same('<root></root>', (new Output\Xml([], 'root'))->serialization());
	}

	public function testAppendingToEmptyXml() {
		Assert::same(
			'<root><simple>SIMPLE</simple></root>',
			(new Output\Xml([], 'root'))->with('simple', 'SIMPLE')->serialization()
		);
	}

	public function testFirstlyStatedNodesWithPrecendence() {
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

	public function testWrappingStatedNodes() {
		Assert::same(
			'<root><AAA><XXX><name>Dominik</name></XXX></AAA></root>',
			(new Output\Xml(['name' => 'Dominik'], 'root'))
			->with('XXX')
			->with('AAA')
			->serialization()
		);
	}

	public function testAddingParentNodes() {
		Assert::same(
			'<root><OUTER><name>Dominik</name><XXX><xxx_inner><who>xxx</who></xxx_inner></XXX><INNER><who>me</who></INNER></OUTER></root>',
			(new Output\Xml(['name' => 'Dominik'], 'root'))
			->with('XXX', ['xxx_inner' => ['who' => 'xxx']])
			->with('INNER', ['who' => 'me'])
			->with('OUTER')
			->serialization()
		);
	}

	public function testAdjustingUsingAnonymousFunction() {
		Assert::same(
			'<root><chars>FOO</chars><time>2015-01-01</time></root>',
			(new Output\Xml([], 'root'))
			->with('time', '2015-01-01')
			->with('chars', 'foo')
			->adjusted('chars', function(string $chars) {
				return strtoupper($chars);
			})
			->serialization()
		);
	}

	public function testAdjustingUsingStringCallback() {
		Assert::same(
			'<root><chars>FOO</chars><time>2015-01-01</time></root>',
			(new Output\Xml([], 'root'))
			->with('time', '2015-01-01')
			->with('chars', 'foo')
			->adjusted('chars', 'strtoupper')
			->serialization()
		);
	}

	public function testAdjustingStatedValues() {
		Assert::same(
			'<root><chars>FOO</chars><time>2015-01-01</time></root>',
			(new Output\Xml(['chars' => 'foo'], 'root'))
			->with('time', '2015-01-01')
			->adjusted('chars', 'strtoupper')
			->serialization()
		);
	}

	public function testIgnoringUnknownTagToBeAdjusted() {
		Assert::noError(function() {
			(new Output\Xml(['chars' => 'foo'], 'root'))
				->with('time', '2015-01-01')
				->adjusted('foo', 'strtoupper');
		});
	}
}

(new Xml())->run();