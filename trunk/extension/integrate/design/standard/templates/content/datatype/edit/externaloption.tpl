{default attribute_base=ContentObjectAttribute}
<select name="{$attribute_base}_data_int_{$attribute.id}">
{section loop=$attribute.content.options}
<option value="{$item.val|wash(xhtml)}" {section show=eq($attribute.content.value, $item.val)} selected{/section}>{$item.label|wash(xhtml)}</option>
{/section}
</select>
{/default}
