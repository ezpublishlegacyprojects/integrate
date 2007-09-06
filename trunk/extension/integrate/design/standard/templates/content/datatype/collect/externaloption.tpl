{default attribute_base=ContentObjectAttribute}
{def $value=cond( is_set( $#collection_attributes[$attribute.id] ), $#collection_attributes[$attribute.id].data_int, $attribute.content.default)}

<select name="{$attribute_base}_data_int_{$attribute.id}">
<option value="">Select option...</option>

{section loop=$attribute.content.options}
<option value="{$item.val|wash(xhtml)}"{if eq($value, $item.val)} selected="selected"{/if}>{$item.label|wash(xhtml)}</option>

{/section}
</select>
{/default}
