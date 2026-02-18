<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Solar Quotation</title>
</head>

<body style="font-family:Arial,sans-serif;background:#f4f7fb;padding:20px">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#fff;border-radius:8px;padding:24px">

                    <tr>
                        <td>
                            <h2 style="color:#1E4D8D;margin-bottom:8px">
                                Your Solar Quotation
                            </h2>

                            <p style="color:#555;font-size:14px">
                                Dear {{ $customer->name }},
                            </p>

                            <p style="color:#555;font-size:14px">
                                Thank you for showing interest in installing a solar power system.
                                Based on your requirement, we have prepared a detailed quotation for you.
                            </p>

                            <hr style="margin:20px 0">

                            <h4 style="margin-bottom:10px">📦 Suggested Solar Package</h4>

                            <table width="100%" style="font-size:14px">
                                {{-- <tr>
            <td>Capacity</td>
            <td align="right"><strong>{{ $quotation->lead->quoteMaster->kw }} kW</strong></td>
        </tr>
        <tr>
            <td>Module</td>
            <td align="right">{{ $quotation->lead->quoteMaster->module }}</td>
        </tr> --}}
                                <tr>
                                    <td>Value</td>
                                    <td style="text-align: right" >
                                        {{ number_format($quotation->lead->quoteMaster->value ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Taxes</td>
                                    <td style="text-align: right" >
                                        {{ number_format($quotation->lead->quoteMaster->taxes ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>MCB/PPA</td>
                                    <td style="text-align: right" >
                                        {{ number_format($quotation->lead->quoteMaster->mcb_ppa ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Metering Cost</td>
                                    <td style="text-align: right" >
                                        {{ number_format($quotation->lead->quoteMaster->metering_cost ?? 0, 2) }}</td>
                                </tr>
                                <tr style="height: 2px;"></tr>
                                <tr>
                                    <td>Payable</td>
                                    <td style="text-align: right" >
                                        {{ number_format($quotation->lead->quoteMaster->payable ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <td style="text-align: right" >
                                        {{ number_format($quotation->discount ?? 0, 2) }}</td>
                                </tr>
                                <tr style="height: 2px;"></tr>
                                <tr>
                                    <td>Final Price</td>
                                    <td style="text-align: right" >
                                        {{ number_format($quotation->final_price ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Subsidy</td>
                                    <td style="text-align: right" >
                                        {{ number_format($quotation->lead->quoteMaster->subsidy ?? 0, 2) }}</td>
                                </tr>
                                <tr style="height: 2px;"></tr>
                                <tr>
                                    <td>Projected</td>
                                    <td style="text-align: right" >
                                        {{ number_format($quotation->final_price - $quotation->lead->quoteMaster->subsidy ?? 0, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Payable</td>
                                    <td align="right"><strong>₹ {{ number_format($quotation->final_price) }}</strong>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin-top:16px;color:#555;font-size:14px">
                                Please find the detailed quotation attached as a PDF with this email.
                            </p>

                            <hr style="margin:20px 0">

                            <h4 style="margin-bottom:10px">🏗️ Our Recent Installations</h4>

                            <ul style="padding-left:18px;font-size:14px;color:#555">
                                @foreach ($recentProjects as $p)
                                    <li>
                                        {{ $p->project_code }} –
                                        {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                    </li>
                                @endforeach
                            </ul>

                            <hr style="margin:20px 0">

                            <p style="font-size:14px;color:#555">
                                Our team will be happy to explain this quotation and guide you further.
                                Please feel free to reply to this email or contact us directly.
                            </p>

                            <p style="font-size:14px;color:#555;margin-top:24px">
                                Warm regards,<br>
                                <strong>{{ config('app.name') }}</strong><br>
                                Solar Installation Team
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
