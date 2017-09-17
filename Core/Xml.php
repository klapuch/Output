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
		return (new class([$this->root => $this->values], '1.0', 'UTF-8') extends \DOMDocument {
			private $values;

			public function __construct(array $values, string $version, string $encoding) {
				parent::__construct($version, $encoding);
				$this->values = $values;
			}

			/**
			 * @param mixed $mixed
			 * @param \DOMElement|null $element
			 */
			private function fromMixed($mixed, \DOMElement $element = null): void {
				$element = $element ?? $this;
				if (is_array($mixed)) {
					foreach ($mixed as $tag => $mixedElement) {
						if (is_int($tag)) {
							if ($tag === 0) {
								$node = $element;
							} else {
								$node = $this->createElement($element->tagName);
								$element->parentNode->appendChild($node);
							}
						} else {
							if (substr($tag, 0, 1) === '@') {
								$element->setAttribute(substr($tag, 1), $mixedElement);
								$node = null;
							} else {
								$node = $this->createElement($tag);
								$element->appendChild($node);
							}
						}
						$this->fromMixed($mixedElement, $node);
					}
				} else {
					if (isset($element->tagName))
						$element->appendChild($this->createTextNode($this->cast($mixed)));
				}
			}

			public function saveXML(\DOMNode $node = null, $options = 0): string {
				$this->fromMixed($this->values);
				return trim($this->withoutDeclaration(parent::saveXML($node, $options)));
			}

			/**
			 * @param mixed $value
			 * @return string
			 */
			private function cast($value): string {
				if (is_bool($value))
					return $value ? 'true' : 'false';
				return (string) $value;
			}

			private function withoutDeclaration(string $xml): string {
				return substr($xml, strpos($xml, '?>') + 2);
			}
		})->saveXML();
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