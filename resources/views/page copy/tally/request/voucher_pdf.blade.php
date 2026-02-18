<ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>EXPORT</TALLYREQUEST>
        <TYPE>OBJECT</TYPE>
        <SUBTYPE>Ledger</SUBTYPE>
        <ID TYPE="Name">{{ $ledger_name }}</ID>
    </HEADER>
    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVFROMDATE>{{ $meta['YearStart'] }}</SVFROMDATE>
                <SVTODATE>{{ $meta['YearEnd'] }}</SVTODATE>
                <SVCURRENTCOMPANY>{{ $meta['CompanyName'] }}</SVCURRENTCOMPANY>
                <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
            </STATICVARIABLES>
            <FETCHLIST>
                <FETCH>OPENINGBALANCE</FETCH>
                <FETCH>CLOSINGBALANCE</FETCH>
            </FETCHLIST>
        </DESC>
    </BODY>
</ENVELOPE>
