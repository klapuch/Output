<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Empty format
 */
final class EmptyFormat implements Format {
	public function with($tag, $value = null): Format {
		return $this;
	}

	public function adjusted($tag, callable $adjustment): Format {
		return $this;
	}

	public function serialization(): string {
		return '';
	}
}