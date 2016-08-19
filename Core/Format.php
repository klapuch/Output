<?php
declare(strict_types = 1);
namespace Klapuch\Output;

interface Format {
	/**
	 * Prepend/Append next element
	 * @param string $tag
	 * @param mixed $value
	 * @return Format
	 */
	public function with(string $tag, $value = null): self;

	/**
	 * Print the content in particular format
	 * @return string
	 */
	public function __toString(): string;

	/**
	 * Single value by the given expression
	 * @param string $expression
	 * @return array
	 */
	public function valueOf(string $expression): array;
}