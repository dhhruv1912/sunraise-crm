<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Panel Invoice</title>

    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 12px;
            color: #1A1A1A;
        }

        .header {
            background: linear-gradient(135deg, #1F628B, #0D3A5B);
            padding: 20px;
            color: white;
            text-align: center;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 1px;
        }

        .sub-header {
            text-align: center;
            margin-top: 5px;
            font-size: 12px;
            color: #F7D483;
        }

        .section {
            margin: 25px 40px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1F628B;
            margin-bottom: 5px;
            border-bottom: 1px solid #E3E8EF;
            padding-bottom: 3px;
        }

        .info-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            color: #174B6B;
            width: 160px;
        }

        .serial-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
        }

        .serial-table th {
            background: #F4B221;
            color: #1A1A1A;
            padding: 6px;
            text-align: left;
            border-bottom: 2px solid #D9991D;
        }

        .serial-table td {
            padding: 6px;
            border-bottom: 1px solid #E3E8EF;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #7E9BB2;
        }

        .qr-box {
            margin-top: 20px;
            text-align: right;
        }

        .qr-box img {
            width: 80px;
            height: 80px;
        }

    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <h2>Solar Panel Batch Invoice</h2>
        <div class="sub-header">Generated Through ARHAM Inventory System</div>
    </div>

    <!-- BATCH INFO -->
    <div class="section">
        <div class="section-title">Batch Information</div>

        <table class="info-table">
            <tr>
                <td class="info-label">Batch No:</td>
                <td>{{ $data['batch_no'] ?? '-' }}</td>

                <td class="info-label">Invoice No:</td>
                <td>{{ $data['invoice_number'] ?? '-' }}</td>
            </tr>

            <tr>
                <td class="info-label">Invoice Date:</td>
                <td>{{ $data['invoice_date'] ?? '-' }}</td>

                <td class="info-label">Item / Model:</td>
                <td>{{ $data['model_no'] ?? '-' }}</td>
            </tr>

            <tr>
                <td class="info-label">Description:</td>
                <td>{{ $data['material_description'] ?? '-' }}</td>

                <td class="info-label">Quantity:</td>
                <td>{{ count($data['serials'] ?? []) }}</td>
            </tr>

            <tr>
                <td class="info-label">Net Weight:</td>
                <td>{{ $data['net_weight'] ?? '-' }}</td>

                <td class="info-label">Gross Weight:</td>
                <td>{{ $data['gross_weight'] ?? '-' }}</td>
            </tr>

            <tr>
                <td class="info-label">Packed Size:</td>
                <td colspan="3">{{ $data['packed_size'] ?? '-' }}</td>
            </tr>
        </table>

        @if(isset($qrBase64))
        <div class="qr-box">
            <img src="data:image/png;base64,{{ $qrBase64 }}">
        </div>
        @endif
    </div>

    <!-- SERIAL NUMBERS -->
    <div class="section">
        <div class="section-title">Serial Numbers</div>

        <table class="serial-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Serial Number</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['serials'] ?? [] as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $s }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <!-- FOOTER -->
    <div class="footer">
        ARHAM Renewtech Pvt. Ltd. — Solar Asset Tracking System • Generated on {{ now()->format('d M Y H:i') }}
    </div>

</body>
</html>
