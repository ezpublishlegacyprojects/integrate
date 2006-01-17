{section loop=$attribute.content.options}
{section show=eq($attribute.content.value, $item.val)}{$item.label|wash(xhtml)}{/section}
{/section}

