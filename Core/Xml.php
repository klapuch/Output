<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Values printed in XML format
 */
final class Xml implements Format {
	const EMPTY_XML = '';
	const EMPTY_MATCH = [];
	private $root;
	private $values;

	public function __construct(string $root = null, array $values = []) {
		$this->root = $root;
		$this->values = $values;
	}

	public function with(string $tag, $value = null): Format {
		if($value === null)
			return new self($this->root, [$tag => $this->values]);
		return new self($this->root, $this->values + [$tag => $value]);
	}

	public function __toString(): string {
		if($this->root === null) {
			return array_reduce(
				array_keys($this->values),
				function(string $xml, string $tag): string {
					$xml .= $this->element(
						$tag,
						$this->isArray($this->values[$tag])
							? new self(null, $this->values[$tag])
							: $this->toXml((string)$this->values[$tag])
					);
					return $xml;
				},
				self::EMPTY_XML
			);
		}
		return $this->element($this->root, new self(null, $this->values));
	}

	public function valueOf(string $expression): array {
		$xml = new \DOMDocument();
		$xml->loadXML((string)$this);
		return array_reduce(
			iterator_to_array((new \DOMXPath($xml))->query($expression)),
			function($matches, \DOMNode $node) {
				$matches[] = $node->nodeValue;
				return $matches;
			},
			self::EMPTY_MATCH
		);
	}

	/**
	 * Element with tag and its value
	 * If the tag is numeric, skip it
	 * @param string $tag
	 * @param string|self $value
	 * @return string
	 */
	private function element(string $tag, $value) {
		if(is_numeric($tag))
			return $value;
		return sprintf('<%1$s>%2$s</%1$s>', $tag, $value);
	}

	/**
	 * Check if the given value is an array
	 * @param mixed $value
	 * @return bool
	 */
	private function isArray($value): bool {
		return (array)$value === $value;
	}

	/**
	 * Value satisfying XML standards
	 * @param string $value
	 * @return string
	 */
	private function toXml(string $value): string {
		return htmlspecialchars($value, ENT_XML1, 'UTF-8');
	}
}