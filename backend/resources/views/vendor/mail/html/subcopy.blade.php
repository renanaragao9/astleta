<table class="subcopy" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
@isset($actionText)
<x-slot:subcopy>
@lang(
"Se você estiver tendo problemas para clicar no \":actionText\" botão, copie e cole o URL abaixo\n".
'em seu navegador:',
[
'actionText' => $actionText,
]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</td>
</tr>
</table>
