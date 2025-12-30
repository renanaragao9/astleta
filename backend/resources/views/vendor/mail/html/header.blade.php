@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://seuracha.s3.sa-east-1.amazonaws.com/email/logo.png" class="logo" alt="Logo da empresa SeuRacha">
@else
<img src="https://seuracha.s3.sa-east-1.amazonaws.com/email/logo.png" class="logo" alt="Logo da empresa SeuRacha">
@endif
</a>
</td>
</tr>
