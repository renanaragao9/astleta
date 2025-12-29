@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://astleta.s3.sa-east-1.amazonaws.com/my/logo.png" class="logo" alt="Logo da empresa astleta">
@else
<img src="https://astleta.s3.sa-east-1.amazonaws.com/my/logo.png" class="logo" alt="Logo da empresa astleta">
@endif
</a>
</td>
</tr>
