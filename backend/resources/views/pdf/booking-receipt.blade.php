<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Reserva - {{ $booking->booking_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Calibri, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
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
            text-align: center;
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
            font-size: 12px;
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

        .pricing-section {
            background: #f5f5f5;
            padding: 8px;
            margin: 5px 0;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 12px;
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
            padding: 8px 10px;
            font-size: 10px;
            color: #333;
            margin-top: 10px;
            line-height: 1.5;
        }

        .fiscal-note strong {
            display: block;
            margin-bottom: 3px;
            color: #2c3e50;
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

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CONFIRMAÇÃO DE RESERVA</h1>
            <p>Locação de Campo Esportivo</p>
            <div class="receipt-number">Nº {{ $booking->booking_number }}</div>
        </div>

        <div class="company-info">
            <strong>ASTLETA</strong><br>
            <strong>astletacontato@gmail.com</strong><br>
        </div>

        <div class="content">
            <div class="section">
                <div class="section-title">Dados da Transação</div>

                <div class="info-row">
                    <span class="info-label">Reserva Criada em:</span>
                    <span class="info-value">{{ isset($booking->created_at) ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i:s') : 'N/A' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Data e Hora da Reserva:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }} às {{ substr($booking->start_time, 0, 5) }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Período:</span>
                    <span class="info-value">{{ substr($booking->start_time, 0, 5) }} às {{ substr($booking->end_time, 0, 5) }}{{ $booking->duration_minutes ? ' · ' . $booking->duration_minutes . ' min' : '' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Status da Reserva:</span>
                    <span class="info-value">
                       {{ ucfirst($booking->booking_status) }}
                    </span>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Detalhes do Serviço</div>
                
                <div class="info-row">
                    <span class="info-label">Campo Alugado:</span>
                    <span class="info-value">{{ $booking->field->name ?? 'N/A' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Local/Parceiro:</span>
                    <span class="info-value">{{ $booking->field->company->name ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Dados do Cliente</div>
                
                <div class="info-row">
                    <span class="info-label">Nome:</span>
                    <span class="info-value">{{ $booking->user->name ?? 'N/A' }}</span>
                </div>

                @if($booking->user->email ?? null)
                <div class="info-row">
                    <span class="info-label">E-mail:</span>
                    <span class="info-value">{{ $booking->user->email }}</span>
                </div>
                @endif

                @if($booking->user->phone ?? null)
                <div class="info-row">
                    <span class="info-label">Telefone:</span>
                    <span class="info-value">{{ $booking->user->phone }}</span>
                </div>
                @endif
            </div>

            <div class="section">
                <div class="section-title">Informações de Pagamento</div>
                
                <div class="pricing-section">
                    <div class="price-row">
                        <span class="price-label">Valor da Reserva:</span>
                        <span class="price-value">R$ {{ number_format($booking->field->price_per_hour ?? 0, 2, ',', '.') }}</span>
                    </div>

                     <div class="price-row">
                        <span class="price-label">Tempo Extra (30min):</span>
                        <span class="price-value">R$ {{ number_format($booking->field->extra_hour_price ?? 0, 2, ',', '.') }}</span>
                    </div>


                    @if($booking->discount_amount && $booking->discount_amount > 0)
                    <div class="price-row">
                        <span class="price-label">Desconto Aplicado:</span>
                        <span class="price-value">-R$ {{ number_format($booking->discount_amount, 2, ',', '.') }}</span>
                    </div>
                    @endif

                    <div class="price-row total">
                        <span class="price-label">VALOR TOTAL PAGO:</span>
                        <span class="price-value">R$ {{ number_format($booking->total_amount ?? 0, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="info-row" style="margin-top: 8px;">
                    <span class="info-label">Forma de Pagamento:</span>
                    <span class="info-value">{{ $booking->paymentForm->name ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="fiscal-note">
                <strong>INFORMAÇÕES IMPORTANTES:</strong>
                Este é um comprovante de reserva. O pagamento deverá ser realizado diretamente com o estabelecimento parceiro ({{ $booking->field->company->name ?? 'parceiro' }}) no horário da reserva. A ASTLETA é apenas uma plataforma de intermediação. Certifique-se de receber um recibo/nota fiscal do estabelecimento após o pagamento.
            </div>
        </div>

        <div class="footer">
            <div class="footer-text">Obrigado por utilizar a ASTLETA!</div>
            <div class="footer-text">Documento gerado em {{ now()->format('d/m/Y às H:i:s') }}</div>
            <div class="footer-logo">ASTLETA</div>
        </div>
    </div>
</body>
</html>