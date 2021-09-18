<?php

use Latte\Runtime as LR;

/** source: template.latte */
final class Template496f377b82 extends Latte\Runtime\Template
{

	public function main(): array
	{
		extract($this->params);
		echo '<h1>';
		echo LR\Filters::escapeHtmlText($title) /* line 1 */;
		echo '</h1>
<ul>
';
		$iterations = 0;
		foreach ($items as $item) /* line 3 */ {
			echo '    <li>';
			echo LR\Filters::escapeHtmlText($item) /* line 4 */;
			echo '</li>
';
			$iterations++;
		}
		echo '</ul>';
		return get_defined_vars();
	}


	public function prepare(): void
	{
		extract($this->params);
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			foreach (array_intersect_key(['item' => '3'], $this->params) as $ʟ_v => $ʟ_l) {
				trigger_error("Variable \$$ʟ_v overwritten in foreach on line $ʟ_l");
			}
		}
		
	}

}
