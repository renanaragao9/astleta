<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cupom - Comanda {{ $tab->code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            background: white;
            width: 80mm;
            margin: 0 auto;
        }

        .cupom {
            padding: 5px;
            max-width: 70mm;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .double-divider {
            border-top: 2px solid #000;
            margin: 5px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin: 5px 0;
        }

        .items-table th, .items-table td {
            border: none;
            padding: 2px;
            text-align: center;
        }

        .items-table th {
            font-weight: bold;
        }

        .items-table .qty {
            width: 20px;
        }

        .items-table .desc {
            text-align: left;
        }

        .items-table .price, .items-table .sub {
            width: 40px;
        }

        .items-table .obs {
            font-size: 8px;
            font-style: italic;
        }

        .divider-cell {
            border-top: 1px dashed #000;
            padding: 0;
            height: 5px;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-weight: bold;
        }

        .footer-text {
            font-size: 8px;
            text-align: center;
            margin: 3px 0;
        }

        .cut-line {
            border-top: 1px dashed #000;
            margin: 10px 0;
            text-align: center;
            font-size: 8px;
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <div class="cupom">
        <div class="center bold">
            {{ $tab->company->name ?? 'NOME DA EMPRESA' }}
        </div>
        <div class="center">
            CUPOM NÃO FISCAL
        </div>
        <div class="center">
            Comanda: {{ $tab->code }}
        </div>
        <div class="center">
            Cliente: {{ $tab->customer_name }}
        </div>
        <div class="center">
            Data: {{ $tab->opened_at->format('d/m/Y H:i') }}
        </div>

        <div class="divider"></div>

        @if($tab->tabItems && $tab->tabItems->count() > 0)
        <div class="bold center">ITENS DA COMANDA</div>
        <div class="divider"></div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="qty">Qtd</th>
                    <th class="desc">Desc.</th>
                    <th class="price">Preço</th>
                    <th class="sub">Sub</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tab->tabItems as $item)
                <tr>
                    <td class="qty">{{ $item->quantity }}</td>
                    <td class="desc">
                        {{ $item->product->name ?? 'Produto' }}
                        @if($item->observation)
                        <br><span class="obs">Obs: {{ $item->observation }}</span>
                        @endif
                    </td>
                    <td class="price">{{ number_format($item->product->price ?? 0, 2, ',', '.') }}</td>
                    <td class="sub">{{ number_format($item->total, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="divider-cell"></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>
        @endif

        <div class="total-line">
            <span>TOTAL ITENS:</span>
            <span>{{ $tab->tabItems ? $tab->tabItems->count() : 0 }}</span>
        </div>

        <div class="total-line">
            <span>VALOR TOTAL:</span>
            <span>R$ {{ number_format($tab->total_amount, 2, ',', '.') }}</span>
        </div>

        @if($tab->paymentForm)
        <div class="total-line">
            <span>PAGAMENTO:</span>
            <span>{{ $tab->paymentForm->name }}</span>
        </div>
        @endif

        <div class="double-divider"></div>

        <div class="center bold">
            STATUS: {{ strtoupper($tab->status) }}
        </div>

        @if($tab->closed_at)
        <div class="center">
            Fechada em: {{ $tab->closed_at->format('d/m/Y H:i') }}
        </div>
        @endif

        <div class="divider"></div>

        <div class="footer-text">
            OBRIGADO PELA PREFERÊNCIA!
        </div>

        <div class="footer-text">
            Este cupom não tem valor fiscal
        </div>

        <div class="footer-text">
            Emitido em: {{ now()->format('d/m/Y H:i:s') }}
        </div>

        <div class="cut-line">
            ------------------ CORTE AQUI ------------------
        </div>
    </div>
</body>
</html>