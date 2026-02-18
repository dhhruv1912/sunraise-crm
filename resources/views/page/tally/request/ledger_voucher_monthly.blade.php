{{-- <ENVELOPE>
    <HEADER>
        <TALLYREQUEST>EXPORT</TALLYREQUEST>
        <TYPE>COLLECTION</TYPE>
        <ID>Cash Flow</ID>
    </HEADER>

    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVCURRENTCOMPANY>{{ $meta['CompanyName'] }}</SVCURRENTCOMPANY>
                <SVFROMDATE>{{ $meta['YearStart'] }}</SVFROMDATE>
                <SVTODATE>{{ $meta['YearEnd'] }}</SVTODATE>
                <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
            </STATICVARIABLES>

            <TDL>
                <TDLMESSAGE>
                    <COLLECTION NAME="Cash Flow">
                        <TYPE>Voucher</TYPE>
                        <FETCH>
                            *
                        </FETCH>
                    </COLLECTION>
                </TDLMESSAGE>
            </TDL>
        </DESC>
    </BODY>
</ENVELOPE> --}}
<ENVELOPE>
    <HEADER>
        <TALLYREQUEST>EXPORT</TALLYREQUEST>
        <TYPE>COLLECTION</TYPE>
        <ID>Monthly Vouchers</ID>
    </HEADER>

    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVCURRENTCOMPANY>{{ $meta['CompanyName'] }}</SVCURRENTCOMPANY>
                <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
            </STATICVARIABLES>

            <TDL>
                <TDLMESSAGE>

                    <COLLECTION NAME="Monthly Vouchers" ISMODIFY="No">
                        <TYPE>Voucher</TYPE>
                        <FETCH>
                            DATE,
                            ALLLEDGERENTRIES.LIST
                        </FETCH>
                    </COLLECTION>

                </TDLMESSAGE>
            </TDL>
        </DESC>
    </BODY>
</ENVELOPE>
