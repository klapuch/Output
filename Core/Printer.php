<?php
declare(strict_types = 1);
namespace Klapuch\Output;

interface Printer {
	/**
	 * Add an next element to print
	 * @param string $tag
	 * @param mixed $value
	 * @return self
	 */
	public function with(string $tag, $value = null): self;

	/**
	 * Print the content
	 * @return string
	 */
	public function __toString(): string;
}