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

final class WrappedXml extends Tester\TestCase {
    public function testWrappingEverything() {
        Assert::same(
            '<root><a>A</a><b>B</b></root>',
            (string)new Output\WrappedXml(
                'root',
                new Output\FakeFormat('<a>A</a>'),
                new Output\FakeFormat('<b>B</b>')
            )
        );
    }

    public function testWithAppliedOnEveryElement() {
        Assert::same(
            '<root><a>A</a>|x|X|<b>B</b>|x|X|</root>',
            (string)(new Output\WrappedXml(
                'root',
                new Output\FakeFormat('<a>A</a>'),
                new Output\FakeFormat('<b>B</b>')
            ))->with('x', 'X')
        );
    }

}

(new WrappedXml())->run();
