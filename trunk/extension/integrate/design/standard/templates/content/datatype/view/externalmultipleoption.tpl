{default seperator='<br />'}
{def $items=array()}
{foreach $attribute.content.options as $option}
  {if $attribute.content.value|contains($option.val)}{set $items=$items|append($option.label|wash)}{/if}
{/foreach}
{$items|implode($seperator)}
{undef $items}
{/default}
