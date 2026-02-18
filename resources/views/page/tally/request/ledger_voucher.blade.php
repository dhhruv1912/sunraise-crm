{{-- <ENVELOPE>
    <HEADER>
        <TALLYREQUEST>Export Data</TALLYREQUEST>
		<TYPE>Collection</TYPE>
    </HEADER>

    <BODY>
        <EXPORTDATA>
            <REQUESTDESC>
                <STATICVARIABLES>
                    <SVFROMDATE>{{ $meta['YearStart'] }}</SVFROMDATE>
                    <SVTODATE>{{ $meta['YearEnd'] }}</SVTODATE>
                    <SVCURRENTCOMPANY>{{ $meta['CompanyName'] }}</SVCURRENTCOMPANY>
                    <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
                    <LEDGERNAME>{{ $ledger_name }}</LEDGERNAME>
                    <SVViewName>Accounting Voucher View</SVViewName>
                </STATICVARIABLES>
                <REPORTNAME>Ledger Vouchers</REPORTNAME>
            </REQUESTDESC>
        </EXPORTDATA>
    </BODY>
</ENVELOPE> --}}
{{-- <ENVELOPE>
    <HEADER>
        <TALLYREQUEST>Export Data</TALLYREQUEST>
        <TYPE>Report</TYPE>
    </HEADER>

    <BODY>
        <EXPORTDATA>
            <REQUESTDESC>
                <REPORTNAME>Ledger Vouchers</REPORTNAME>
                <STATICVARIABLES>
                    <SVFROMDATE>{{ $meta['YearStart'] }}</SVFROMDATE>
                    <SVTODATE>{{ $meta['YearEnd'] }}</SVTODATE>
                    <SVCURRENTCOMPANY>{{ $meta['CompanyName'] }}</SVCURRENTCOMPANY>
                    <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
                    <LEDGERNAME>{{ $ledger_name }}</LEDGERNAME>
                    <SVViewName>Accounting Voucher View</SVViewName>
                </STATICVARIABLES>
            </REQUESTDESC>
        </EXPORTDATA>
    </BODY>
</ENVELOPE> --}}
<ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>Export</TALLYREQUEST>
        <TYPE>Collection</TYPE>
        <ID>LedgerVouchers</ID>
    </HEADER>

    <BODY>
        <DESC>
            <STATICVARIABLES>
                {{-- <SVFROMDATE>{{ $meta['YearStart'] }}</SVFROMDATE>
                <SVTODATE>{{ $meta['YearEnd'] }}</SVTODATE> --}}
                <SVCURRENTCOMPANY>{{ $meta['CompanyName'] }}</SVCURRENTCOMPANY>
                <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
            </STATICVARIABLES>

            <TDL>
                <TDLMESSAGE>

                    <COLLECTION NAME="LedgerVouchers">
                        <TYPE>Voucher</TYPE>
                        <FILTER>LedgerFilter</FILTER>
                    </COLLECTION>

                    <SYSTEM TYPE="Formulae" NAME="LedgerFilter">
                        $LedgerName = "{{ $ledger_name }}"
                    </SYSTEM>

                </TDLMESSAGE>
            </TDL>

        </DESC>
    </BODY>
</ENVELOPE>
