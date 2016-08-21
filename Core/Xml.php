<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Values printed in XML format
 */
final class Xml implements Format {
	const EMPTY_XML = '';
	const EMPTY_MATCH = [];
	private $values;
	private $root;

	public function __construct(array $values, string $root = null) {
		$this->values = $values;
		$this->root = $root;
	}

	public function with(string $tag, $value = null): Format {
		if($value === null)
			return new self([$tag => $this->values], $this->root);
		return new self($this->values + [$tag => $value], $this->root);
	}

	public function __toString(): string {
		if($this->root === null) {
			return array_reduce(
				array_keys($this->values),
				function(string $xml, string $tag): string {
					$xml .= $this->element(
						$tag,
						$this->isArray($this->values[$tag])
							? new self($this->values[$tag])
							: $this->toXml((string)$this->values[$tag])
					);
					return $xml;
				},
				self::EMPTY_XML
			);
		}
		return $this->element($this->root, new self($this->values));
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
	 * Faster version of is_array (because of the recursion)
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
