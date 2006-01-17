{default attribute_base=ContentObjectAttribute}
<select name="{$attribute_base}_data_int_{$attribute.id}[]" size="5" multiple="multiple">
{section loop=$attribute.content.options}
<option value="{$item.val|wash(xhtml)}" {section show=$attribute.content.value|contains($item.val)} selected{/section}>{$item.label|wash(xhtml)}</option>
{/section}
</select>
{/default}
