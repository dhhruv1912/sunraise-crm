<ENVELOPE>
    <HEADER>
        <TALLYREQUEST>Export Data</TALLYREQUEST>
        <TYPE>COLLECTION</TYPE>
    </HEADER>
    <BODY>
        <EXPORTDATA>
            <REQUESTDESC>
                <STATICVARIABLES>
                    <SVFROMDATE>{{ $meta['YearStart'] }}</SVFROMDATE>
                    <SVTODATE>{{ $meta['YearEnd'] }}</SVTODATE>
                    <SVCURRENTCOMPANY>{{ $meta['CompanyName'] }}</SVCURRENTCOMPANY>
                    <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
                    <STOCKITEMNAME>{{ $meta['stock_name'] }}</STOCKITEMNAME>
                    <SVViewName>Accounting Voucher View</SVViewName>
                </STATICVARIABLES>
                <REPORTNAME>Stock Vouchers</REPORTNAME>
            </REQUESTDESC>
        </EXPORTDATA>
    </BODY>
</ENVELOPE>
