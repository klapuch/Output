<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Values printed in XML format
 */
final class Xml implements Format {
	private $values;
	private $root;

	public function __construct(array $values, string $root) {
		$this->values = $values;
		$this->root = $root;
	}

	public function with($tag, $content = null): Format {
		return new self($this->values + [$tag => $content], $this->root);
	}

	public function adjusted($tag, callable $adjustment): Format {
		if (!$this->adjustable($tag, $this->values))
			return $this;
		return new self(
			[$tag => call_user_func($adjustment, $this->values[$tag])] + $this->values,
			$this->root
		);
	}

	public function serialization(): string {
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->appendChild(
			array_reduce(
				array_keys($this->values),
				function(\DOMElement $root, $tag) use ($dom): \DOMElement {
					if (substr($tag, 0, 1) === '@')
						$root->setAttribute(substr($tag, 1), $this->values[$tag]);
					else
						$root->appendChild($dom->createElement($tag, $this->toXml($this->values[$tag])));
					return $root;
				},
				$dom->createElement($this->root)
			)
		);
		return trim($this->withoutDeclaration($dom->saveXML()));
	}

	/**
	 * XML string without declaration <?xml ... ?>
	 * @param string $xml
	 * @return string
	 */
	private function withoutDeclaration(string $xml): string {
		return substr($xml, strpos($xml, '?>') + 2);
	}

	/**
	 * Value satisfying XML standards
	 * @param mixed $content
	 * @return string
	 */
	private function toXml($content): string {
		return htmlspecialchars($this->cast($content), ENT_XML1, 'UTF-8');
	}

	/**
	 * Cast the value to be proper XML and XSD proof
	 * @param mixed $value
	 * @return string
	 */
	private function cast($value): string {
		if (is_bool($value))
			return $value ? 'true' : 'false';
		return (string) $value;
	}

	/**
	 * Is the tag adjustable?
	 * @param string $tag
	 * @param array $values
	 * @return bool
	 */
	private function adjustable(string $tag, array $values): bool {
		return isset($values[$tag]);
	}
}