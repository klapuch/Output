<?php
declare(strict_types = 1);
namespace Klapuch\Output;

interface Format {
	/**
	 * Prepend/Append next element
	 * @param mixed $tag
	 * @param mixed $content
	 * @return \Klapuch\Output\Format
	 */
	public function with($tag, $content = null): self;

	/**
	 * Print the content in particular format
	 * @return string
	 */
	public function serialization(): string;

	/**
	 * Adjusted format within the given tag and applicable adjustment
	 * @param mixed $tag
	 * @param callable $adjustment
	 * @return \Klapuch\Output\Format
	 */
	public function adjusted($tag, callable $adjustment): self;
}