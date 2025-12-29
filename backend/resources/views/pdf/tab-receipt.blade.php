<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Comprovante de Comanda - {{ $tab->code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Calibri, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 550px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
        }

        .header {
            background: #2c3e50;
            color: white;
            padding: 12px 15px;
            text-align: center;
            border-bottom: 3px solid #1a252f;
        }

        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header p {
            font-size: 9px;
            opacity: 0.9;
            margin: 0;
        }

        .receipt-number {
            font-size: 8px;
            margin-top: 3px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .company-info {
            background: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }

        .company-info strong {
            color: #2c3e50;
        }

        .content {
            padding: 15px;
        }

        .section {
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .section-title {
            font-size: 9px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            background: #f0f0f0;
            padding: 4px 8px;
            border-left: 3px solid #2c3e50;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 10px;
        }

        .info-label {
            color: #555;
            font-weight: bold;
        }

        .info-value {
            color: #333;
            text-align: right;
            flex: 1;
            padding-left: 10px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 9px;
        }

        .items-table thead {
            background: #f0f0f0;
            border-bottom: 2px solid #2c3e50;
        }

        .items-table th {
            padding: 6px;
            text-align: left;
            font-weight: bold;
            color: #2c3e50;
            border-right: 1px solid #ddd;
        }

        .items-table th:last-child {
            border-right: none;
            text-align: right;
        }

        .items-table td {
            padding: 6px;
            border-bottom: 1px solid #e0e0e0;
            border-right: 1px solid #ddd;
        }

        .items-table td:last-child {
            border-right: none;
            text-align: right;
            font-weight: bold;
        }

        .items-table tbody tr:hover {
            background: #f9f9f9;
        }

        .item-name {
            font-weight: 500;
            color: #333;
        }

        .item-qty {
            text-align: center;
            width: 40px;
        }

        .item-price {
            text-align: right;
            width: 60px;
        }

        .item-total {
            text-align: right;
            width: 70px;
        }

        .pricing-section {
            background: #f9f9f9;
            padding: 8px;
            margin: 8px 0;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 10px;
        }

        .price-row.subtotal {
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .price-row.total {
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 5px;
            font-weight: bold;
            font-size: 11px;
            color: #2c3e50;
        }

        .price-label {
            color: #555;
        }

        .price-value {
            color: #333;
            font-weight: bold;
            text-align: right;
        }

        .fiscal-note {
            background: #fff9e6;
            border: 1px solid #ffd966;
            padding: 8px 10px;
            font-size: 8px;
            color: #856404;
            margin-top: 10px;
            border-radius: 3px;
            line-height: 1.5;
        }

        .fiscal-note strong {
            display: block;
            margin-bottom: 3px;
            color: #333;
        }

        .footer {
            background: #f8f9fa;
            padding: 10px 15px;
            text-align: center;
            border-top: 2px solid #2c3e50;
            font-size: 8px;
        }

        .footer-text {
            color: #666;
            margin-bottom: 2px;
        }

        .footer-logo {
            color: #2c3e50;
            margin-top: 5px;
            font-weight: bold;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>COMPROVANTE DE COMANDA</h1>
            <p>Consumo de Alimentos e Bebidas</p>
            <div class="receipt-number">Nº {{ $tab->code }}</div>
        </div>

        <div class="company-info">
            <strong>ASTLETA - Plataforma de Intermediação</strong><br>
            {{-- CNPJ: {{ env('COMPANY_CNPJ', 'XX.XXX.XXX/XXXX-XX') }} | {{ env('COMPANY_EMAIL', 'contato@astleta.com.br') }} --}}
        </div>

        <div class="content">
            <div class="section">
                <div class="section-title">Dados da Comanda</div>
                
                <div class="info-row">
                    <span class="info-label">Empresa:</span>
                    <span class="info-value">{{ $tab->company->name ?? 'N/A' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Data/Hora:</span>
                    <span class="info-value">{{ $tab->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        @if($tab->status === 'aberta')
                            Aberta
                        @elseif($tab->status === 'fechada')
                            Fechada
                        @else
                            {{ ucfirst($tab->status) }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Itens da Comanda</div>

                @if($tab->tabItems && count($tab->tabItems) > 0)
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th class="item-qty">Qtd</th>
                            <th class="item-price">Valor</th>
                            <th class="item-total">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tab->tabItems as $item)
                        <tr>
                            <td class="item-name">{{ $item->product->name ?? 'Produto' }}</td>
                            <td class="item-qty">{{ $item->quantity }}</td>
                            <td class="item-price">R$ {{ number_format($item->product->price ?? 0, 2, ',', '.') }}</td>
                            <td class="item-total">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="info-row" style="color: #999;">
                    <span>Nenhum item cadastrado</span>
                </div>
                @endif
            </div>

            <div class="section">
                <div class="section-title">Resumo de Valores</div>
                
                <div class="pricing-section">
                    <div class="price-row total">
                        <span class="price-label">TOTAL:</span>
                        <span class="price-value">R$ {{ number_format($tab->total_amount ?? 0, 2, ',', '.') }}</span>
                    </div>
                </div>

                @if($tab->paymentForm)
                <div class="info-row">
                    <span class="info-label">Forma de Pagamento:</span>
                    <span class="info-value">{{ $tab->paymentForm->name ?? 'N/A' }}</span>
                </div>
                @endif
            </div>

            <div class="fiscal-note">
                <strong>INFORMAÇÕES IMPORTANTES:</strong>
                Este é um comprovante de comanda de consumo. Guarde este comprovante como comprovação de sua consumação. O estabelecimento é responsável pela emissão de documentos fiscais quando aplicável. A ASTLETA é uma plataforma de intermediação.
            </div>
        </div>

        <div class="footer">
            <div class="footer-text">Obrigado por sua compra!</div>
            <div class="footer-text">Documento gerado em {{ now()->format('d/m/Y às H:i:s') }}</div>
            <div class="footer-logo">ASTLETA</div>
        </div>
    </div>
</body>
</html>