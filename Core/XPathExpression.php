<?php
declare(strict_types = 1);

namespace Klapuch\Output;

/**
 * Expression evaluated as a XPath
 */
final class XPathExpression implements Expression {
	/** @var string */
	private $expression;

	/** @var \Klapuch\Output\Format */
	private $format;

	public function __construct(string $expression, Format $format) {
		$this->expression = $expression;
		$this->format = $format;
	}

	public function matches(): array {
		$xml = new \DOMDocument();
		$xml->loadXML($this->format->serialization());
		return array_map(static function(\DOMNode $node): string {
			return $node->nodeValue;
		}, iterator_to_array((new \DOMXPath($xml))->query($this->expression)));
	}
}
