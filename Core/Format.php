<?php
declare(strict_types = 1);
namespace Klapuch\Output;

interface Format {
	/**
	 * Prepend/Append next element
	 * @param string $tag
	 * @param string|array $content
	 * @return Format
	 */
	public function with(string $tag, $content = null): self;

	/**
	 * Print the content in particular format
	 * @return string
	 */
	public function __toString(): string;
}