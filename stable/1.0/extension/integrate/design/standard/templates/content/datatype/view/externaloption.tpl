{section show=$attribute.content.value|is_numeric()}
  {section loop=$attribute.content.options}
    {section show=eq($attribute.content.value, $item.val)}{$item.label|wash(xhtml)}{/section}
  {/section}
{section-else}
No option selected
{/section}

