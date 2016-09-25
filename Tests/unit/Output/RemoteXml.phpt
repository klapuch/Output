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

final class RemoteXml extends Tester\TestCase {
    public function testXmlFromExistingSource() {
        Assert::contains(
            '<root><content>a</content></root>',
            (string)new Output\RemoteXml(
                Tester\FileMock::create('<root><content>a</content></root>')
            )
        );
    }

    public function testCorrectEncoding() {
        Assert::contains(
            '<root>Příliš žluťoučký kůň úpěl ďábelské ódy.</root>',
            (string)new Output\RemoteXml(
                Tester\FileMock::create(
                    '<root>Příliš žluťoučký kůň úpěl ďábelské ódy.</root>'
                )
            )
        );
    }

    public function testAddingNewNode() {
        Assert::contains(
            '<outer>OUTER</outer>',
            (string)(new Output\RemoteXml(
                Tester\FileMock::create('<root><content>a</content></root>')
            ))->with('outer', 'OUTER')
        );
    }

    public function testAddingNewNodeWithoutContent() {
        Assert::contains(
            '<outer/>',
            (string)(new Output\RemoteXml(
                Tester\FileMock::create('<root><content>a</content></root>')
            ))->with('outer')
        );
    }
}

(new RemoteXml())->run();
