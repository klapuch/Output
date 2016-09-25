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

final class XPathExpression extends Tester\TestCase {
    public function testExistingExpression() {
        Assert::same(
            ['Dominik'],
            (new Output\XPathExpression(
                'name',
                new Output\FakeFormat('<root><name>Dominik</name></root>')
            ))->matches());
        Assert::same(
            ['Dominik'],
            (new Output\XPathExpression(
                '//name',
                new Output\FakeFormat('<root><name>Dominik</name></root>')
            ))->matches());
    }

    public function testUnknownExpressionWithoutMatch() {
        Assert::same(
            [],
            (new Output\XPathExpression(
                'wtf',
                new Output\FakeFormat('<root><name>Dominik</name></root>')
            ))->matches());
    }
}

(new XPathExpression())->run();
