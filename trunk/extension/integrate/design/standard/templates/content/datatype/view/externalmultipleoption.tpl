{section loop=$attribute.content.options}
{section show=$attribute.content.value|contains($item.val)}{$item.label|wash(xhtml)}<br/>{/section}

{/section}

