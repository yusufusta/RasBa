<?php

use Latte\Runtime as LR;

/** source: template.php */
final class Template00cc6ff69a extends Latte\Runtime\Template
{

	public function main(): array
	{
		extract($this->params);
		echo '<h1>';
		echo LR\Filters::escapeHtmlText($title) /* line 1 */;
		echo '</h1>
';
		return get_defined_vars();
	}

}
