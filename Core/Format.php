<?php
declare(strict_types = 1);
namespace Klapuch\Output;

interface Format {
	/**
	 * Prepend/Append next element
	 * @param string $tag
	 * @param mixed $content
	 * @return Format
	 */
	public function with(string $tag, $content = null): self;

	/**
	 * Print the content in particular format
	 * @return string
	 */
	public function serialization(): string;

	/**
	 * Adjusted format within the given tag and applicable adjustment
	 * @param string $tag
	 * @param callable $adjustment
	 * @return Format
	 */
	public function adjusted(string $tag, callable $adjustment): self;
}