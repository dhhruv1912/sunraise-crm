<ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>EXPORT</TALLYREQUEST>
        <TYPE>COLLECTION</TYPE>
        <ID>All Ledgers</ID>
    </HEADER>

    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVCURRENTCOMPANY>{{ $meta['CompanyName'] }}</SVCURRENTCOMPANY>
                <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
            </STATICVARIABLES>
            <TDL>
                <TDLMESSAGE>
                    <COLLECTION NAME="All Ledgers" ISMODIFY="No">
                        <TYPE>Ledger</TYPE>
                        <FETCH>Name, Parent, ClosingBalance</FETCH>
                    </COLLECTION>
                </TDLMESSAGE>
            </TDL>
        </DESC>
    </BODY>
</ENVELOPE>
