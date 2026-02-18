<ENVELOPE>
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
</ENVELOPE>
