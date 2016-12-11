<?php
declare(strict_types = 1);
namespace Klapuch\Output;

interface Expression {
	/**
	 * Matches given by evaluating the expression
	 * @return array
	 */
	public function matches(): array;
}