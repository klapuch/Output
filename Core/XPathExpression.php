<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Expression evaluated as a XPath
 */
final class XPathExpression implements Expression {
    const EMPTY_MATCH = [];
    private $expression;
    private $format;

    public function __construct(string $expression, Format $format) {
        $this->expression = $expression;
        $this->format = $format;
    }

    public function matches(): array {
        $xml = new \DOMDocument();
        $xml->loadXML((string)$this->format);
        return array_reduce(
            iterator_to_array((new \DOMXPath($xml))->query($this->expression)),
            function($matches, \DOMNode $node) {
                $matches[] = $node->nodeValue;
                return $matches;
            },
            self::EMPTY_MATCH
        );

    }
}
