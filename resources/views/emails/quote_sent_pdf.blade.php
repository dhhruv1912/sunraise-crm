<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #222;
        }

        .wrap {
            max-width: 800px;
            margin: 0 auto;
            padding: 18px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            width: auto;
            height: 50px;
            /* background: #eaf2ff; */
            display: inline-block;
            border-radius: 4px;
            object-fit: contain;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* margin-top: 12px; */
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .total {
            font-weight: 700;
        }

        .meta {
            margin-top: 18px;
            font-size: 12px;
            color: #555;
        }

        .main-divider {
            height: 15px;
            background: #f2af7f;
            margin-top: 5px;
        }

        .title {
            height: 35px;
            background: #567fba;
            color: white;
            text-align: center;
            font-size: 30px;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="header">
            <div>
                {{-- <h1>Quotation</h1>
                <div>Quote No: {{ $quoteNo ?? 'Q-' . $lead->lead_code . '-' . $lead->id }}</div> --}}
                <strong>Name:</strong> {{ $company['COMPANY_NAME'] ?? '-' }} <br>
                <strong>Phone No.:</strong> {{ $company['COMPANY_NUMBER'] ?? '-' }} <br>
                <strong>Email ID:</strong> {{ $company['COMPANY_EMAIL'] ?? '-' }} <br>
                <strong>GSTIN:</strong> {{ $company['COMPANY_GST'] ?? '-' }} <br>
                <strong>Address:</strong> {{ $company['COMPANY_ADDRESS'] ?? '-' }} <br>
            </div>
            <img src="{{ asset('assets/img/logo/logo.png') }}" class="logo" alt="" srcset="">
        </div>

        <div class="main-divider"></div>
        <div class="title">Quotation</div>
        <h3>To:</h3>
        <div style="margin-top:12px">
            <strong>Name:</strong> {{ $lead->customer->name ?? '-' }} <br>
        </div>
        <br>
        <div style="display:flex;align-items: center;justify-content: space-between;">
          <div>
            <strong>Phone:</strong> {{ $lead->customer->mobile ?? '-' }} <br>
            <strong>Email:</strong> {{ $lead->customer->email ?? '-' }}
          </div>
          <div>
            <div style="text-align: right">Quote No: {{ $quoteNo ?? 'Q-' . $lead->lead_code . '-' . $lead->id }}</div>
            <div style="text-align: right">Date: {{ $quotation->created_at }}</div>
          </div>
        </div>
        <h3 style="margin-top:16px;margin-bottom:0">Summary</h3>
        <div class="main-divider" style="background:#567fba"></div>
        <table>
            <thead>
                <tr>
                    <th>SKU / Package</th>
                    <th>Module</th>
                    <th>KW</th>
                    <th>Count</th>
                    <th colspan="2">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($quotation->meta))
                    <tr>
                        <td>{{ $quotation->meta['sku'] ?? '-' }}</td>
                        <td>{{ $quotation->meta['module'] ?? ($lead->module ?? '-') }}</td>
                        <td>{{ $quotation->meta['kw'] ?? ($lead->kw ?? '-') }}</td>
                        <td>{{ $quotation->meta['module_count'] ?? '' }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">Value</td>
                        <td style="text-align: right" colspan="2">{{ number_format($quotation->meta['value'] ?? "-",2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">Taxes</td>
                        <td style="text-align: right" colspan="2">{{ number_format($quotation->meta['taxes'] ?? "-",2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">MCB/PPA</td>
                        <td style="text-align: right" colspan="2">{{ number_format($quotation->meta['mcb_ppa'] ?? "-",2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">Metering Cost</td>
                        <td style="text-align: right" colspan="2">{{ number_format($quotation->meta['metering_cost'] ?? "-",2) }}</td>
                    </tr>
                    <tr style="height: 2px;"></tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">Payable</td>
                        <td style="text-align: right" colspan="2">{{ number_format($quotation->meta['payable'] ?? "-",2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">Discount</td>
                        <td style="text-align: right" colspan="2">{{ number_format($quotation->meta['discount'] ?? 0,2) }}</td>
                    </tr>
                    <tr style="height: 2px;"></tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">Final Price</td>
                        <td style="text-align: right" colspan="2">{{ number_format($quotation->meta['final_price'] ?? "-",2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">Subsidy</td>
                        <td style="text-align: right" colspan="2">{{ number_format($quotation->meta['subsidy'] ?? "-",2) }}</td>
                    </tr>
                    <tr style="height: 2px;"></tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">Projected</td>
                        <td style="text-align: right" colspan="2">{{ (number_format($quotation->meta['final_price'] - $quotation->meta['subsidy'] ?? "-",2)) }}</td>
                    </tr>
                @else
                    <tr>
                        <td>-</td>
                        <td>{{ $lead->module ?? '-' }}</td>
                        <td>{{ $lead->kw ?? '-' }}</td>
                        <td>{{ $lead->mc ?? '-' }}</td>
                        <td>{{ number_format($lead->payable ?? 0, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="meta">
            <p>Notes: {{ $lead->notes ?? '-' }}</p>
        </div>
        {{--
    @if ($projects && $projects->count())
      <h4>Recent Completed Projects</h4>
      <ul>
        @foreach ($projects as $p)
          <li>{{ $p->customer_name ?? '—' }} — {{ $p->project_code ?? "PID-{$p->id}" }}</li>
        @endforeach
      </ul>
    @endif --}}

    </div>
</body>

</html>
