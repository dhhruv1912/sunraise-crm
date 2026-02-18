<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Solar Installation Proposal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 720px;
            background: #ffffff;
            margin: 24px auto;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .header {
            background: #1E4D8D;
            color: #ffffff;
            padding: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
        }
        .section {
            padding: 20px;
        }
        .section h3 {
            margin-top: 0;
            margin-bottom: 12px;
            color: #1E4D8D;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 6px 0;
            vertical-align: top;
        }
        .label {
            color: #64748B;
            width: 45%;
        }
        .value {
            font-weight: 600;
            color: #1A1A1A;
        }
        .price {
            font-size: 18px;
            color: #F28C28;
        }
        .highlight {
            background: #f8fafc;
            padding: 12px;
            border-radius: 6px;
        }
        .projects ul {
            padding-left: 18px;
            margin: 0;
        }
        .projects li {
            margin-bottom: 6px;
        }
        .footer {
            padding: 16px;
            text-align: center;
            font-size: 12px;
            color: #64748B;
            background: #f4f7fb;
        }
    </style>
</head>
<body>

<div class="container">

    {{-- HEADER --}}
    <div class="header">
        <h2>Solar Installation Proposal</h2>
        <div style="font-size:14px; opacity:.9;">
            Based on your site requirements
        </div>
    </div>

    {{-- INTRO --}}
    <div class="section">
        <p>
            Dear <strong>{{ $qr->customer->name }}</strong>,
        </p>

        <p>
            Thank you for contacting us regarding your solar installation requirement.
            Based on the information shared by you, our technical team has reviewed your
            site details and prepared a suitable solar solution for your property.
        </p>
    </div>

    {{-- CUSTOMER REQUIREMENT --}}
    <div class="section">
        <h3>Your Requirement</h3>
        <table>
            <tr>
                <td class="label">Required Capacity</td>
                <td class="value">{{ $qr->kw }} kW</td>
            </tr>
            <tr>
                <td class="label">Installation Type</td>
                <td class="value">{{ ucfirst($qr->type) }}</td>
            </tr>
            @if($qr->budget)
            <tr>
                <td class="label">Budget Range</td>
                <td class="value">₹ {{ number_format($qr->budget) }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- RECOMMENDED PACKAGE --}}
    <div class="section">
        <h3>Recommended Solar Package</h3>

        <div class="highlight">
            <table>
                <tr>
                    <td class="label">System Capacity</td>
                    <td class="value">{{ optional($qr->quoteMaster)->kw }} kW</td>
                </tr>
                <tr>
                    <td class="label">Solar Module</td>
                    <td class="value">{{ optional($qr->quoteMaster)->module }}</td>
                </tr>
                <tr>
                    <td class="label">Number of Panels</td>
                    <td class="value">{{ optional($qr->quoteMaster)->module_count }}</td>
                </tr>
                <tr>
                    <td class="label">Total System Cost</td>
                    <td class="value price">
                        ₹ {{ number_format(optional($qr->quoteMaster)->payable ?? 0) }}
                    </td>
                </tr>
                @if(optional($qr->quoteMaster)->subsidy)
                <tr>
                    <td class="label">Eligible Subsidy</td>
                    <td class="value">
                        ₹ {{ number_format(optional($qr->quoteMaster)->subsidy) }}
                    </td>
                </tr>
                @endif
            </table>
        </div>

        <p style="margin-top:12px;">
            <em>
                This package has been recommended based on your energy requirement,
                site conditions, and long-term performance considerations.
            </em>
        </p>
    </div>

    {{-- SOCIAL PROOF --}}
    <div class="section projects">
        <h3>Recent Installations Completed by Us</h3>
        <ul>
            @foreach($projects as $p)
                <li>
                    {{ $p->project_code }}
                    @if($p->kw)
                        – {{ $p->kw }} kW system
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    {{-- NEXT STEPS --}}
    <div class="section">
        <h3>Next Steps</h3>
        <p>
            Our team will be happy to explain the proposal in detail and
            address any questions you may have regarding installation,
            subsidies, or timelines.
        </p>

        <p>
            Please reply to this email or contact us to proceed further.
        </p>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        This proposal was prepared by our solar engineering team.<br>
        © {{ date('Y') }} Solar Installation Services
    </div>

</div>

</body>
</html>
