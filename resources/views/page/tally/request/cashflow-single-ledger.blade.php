<ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>EXPORT</TALLYREQUEST>
        <TYPE>OBJECT</TYPE>
        <SUBTYPE>Ledger</SUBTYPE>
        <ID TYPE="Name">{{ $ledger_name }} </ID>
    </HEADER>

    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVFROMDATE>{{ $meta['YearStart'] }}</SVFROMDATE>
                <SVTODATE>{{ $meta['YearEnd'] }}</SVTODATE>
                <SVCURRENTCOMPANY>{{ $meta['CompanyName'] }}</SVCURRENTCOMPANY>
                <SVEXPORTFORMAT>BinaryXML</SVEXPORTFORMAT>
                <SVVALUATIONMETHOD TYPE="String"></SVVALUATIONMETHOD>
                <SVBUDGET TYPE="String"></SVBUDGET>
            </STATICVARIABLES>
            <FETCHLIST>
                <FETCH>Name</FETCH>
                <FETCH>Parent</FETCH>
                <FETCH>Closing Balance</FETCH>
                <FETCH>Opening Balance</FETCH>
            </FETCHLIST>
        </DESC>
    </BODY>
</ENVELOPE>