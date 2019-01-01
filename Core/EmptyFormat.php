<?php
declare(strict_types = 1);

namespace Klapuch\Output;

/**
 * Empty format
 */
final class EmptyFormat implements Format {
	/**
	 * @param mixed $tag
	 * @param mixed|null $value
	 * @return \Klapuch\Output\Format
	 */
	public function with($tag, $value = null): Format {
		return $this;
	}

	/**
	 * @param mixed $tag
	 * @param callable $adjustment
	 * @return \Klapuch\Output\Format
	 */
	public function adjusted($tag, callable $adjustment): Format {
		return $this;
	}

	public function serialization(): string {
		return '';
	}
}
