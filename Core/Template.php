<?php
declare(strict_types = 1);
namespace Klapuch\Output;

interface Template {
	/**
	 * Render the template
	 * @param array $variables
	 * @return string
	 */
	public function render(array $variables = []): string;
}