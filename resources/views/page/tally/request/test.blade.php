{{-- <ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>Export</TALLYREQUEST>
        <TYPE>Data</TYPE>
        <ID>List Of Vouchers</ID>
    </HEADER>
    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
                <SVFROMDATE>{{ $meta['YearStart'] }}</SVFROMDATE>
                <SVTODATE>{{ $meta['YearEnd'] }}</SVTODATE>
                <SVCURRENTCOMPANY>{{ $meta['CompanyName'] }}</SVCURRENTCOMPANY>
            </STATICVARIABLES>
            <TDL>
                <TDLMESSAGE>
                    <REPORT ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="List Of Vouchers">
                        <FORMS>List Of Vouchers</FORMS>
                    </REPORT>
                    <FORM ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="List Of Vouchers">
                        <TOPPARTS>List Of Vouchers</TOPPARTS>
                        <XMLTAG>ListOfVouchers</XMLTAG>
                    </FORM>
                    <PART ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="List Of Vouchers">
                        <TOPLINES>List Of Vouchers</TOPLINES>
                        <REPEAT>List Of Vouchers : FormList Of Vouchers</REPEAT>
                        <SCROLLED>Vertical</SCROLLED>
                    </PART>
                    <LINE ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="List Of Vouchers">
                        <LEFTFIELDS>MASTERID</LEFTFIELDS>
                        <LEFTFIELDS>VoucherNumber</LEFTFIELDS>
                        <LEFTFIELDS>Date</LEFTFIELDS>
                    </LINE>
                    <FIELD ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="MASTERID">
                        <SET>$MASTERID</SET>
                        <XMLTAG>MASTERID</XMLTAG>
                    </FIELD>
                    <FIELD ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="VoucherNumber">
                        <SET>$VoucherNumber</SET>
                        <XMLTAG>VoucherNumber</XMLTAG>
                    </FIELD>
                    <FIELD ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="Date">
                        <SET>$Date</SET>
                        <XMLTAG>Date</XMLTAG>
                    </FIELD>
                    <COLLECTION ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="FormList Of Vouchers">
                        <TYPE>Voucher</TYPE>
                        <FILTERS>VoucherType</FILTERS>
                    </COLLECTION>
                    <SYSTEM TYPE="Formulae" NAME="VoucherType">$VoucherTypeName = "Attendance"</SYSTEM>
                </TDLMESSAGE>
            </TDL>
        </DESC>
    </BODY>
</ENVELOPE> --}}
{{-- <ENVELOPE Action="">
    <HEADER>
      <VERSION>1</VERSION>
      <TALLYREQUEST>EXPORT</TALLYREQUEST>
      <TYPE>COLLECTION</TYPE>
      <ID>CUSTOMVOUCHERTYPECOL</ID>
    </HEADER>
    <BODY>
      <DESC>
        <STATICVARIABLES />
        <TDL>
          <TDLMESSAGE>
            <COLLECTION ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="CUSTOMVOUCHERTYPECOL">
              <TYPE>VOUCHERTYPE</TYPE>
            </COLLECTION>
          </TDLMESSAGE>
        </TDL>
      </DESC>
    </BODY>
  </ENVELOPE> --}}
<!--
Generated Using TallyConnector - https://github.com/Accounting-Companion/TallyConnector
Incase of any errors raise a issue here - https://github.com/Accounting-Companion/TallyXmlsGenerator

-->
{{-- <ENVELOPE Action="">
    <HEADER>
      <VERSION>1</VERSION>
      <TALLYREQUEST>EXPORT</TALLYREQUEST>
      <TYPE>DATA</TYPE>
      <ID>MasterTypeStat</ID>
    </HEADER>
    <BODY>
      <DESC>
        <STATICVARIABLES>
          <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
          <SVCURRENTCOMPANY>Jay Enterprise</SVCURRENTCOMPANY>
        </STATICVARIABLES>
        <TDL>
          <TDLMESSAGE>
            <REPORT ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="MasterTypeStat">
              <FORMS>MasterTypeStat</FORMS>
            </REPORT>
            <FORM ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="MasterTypeStat">
              <TOPPARTS>MasterTypeStat</TOPPARTS>
              <XMLTAG>MasterTypeStat.LIST</XMLTAG>
            </FORM>
            <PART ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="MasterTypeStat">
              <TOPLINES>MasterTypeStat</TOPLINES>
              <REPEAT>MasterTypeStat : STATObjects</REPEAT>
              <SCROLLED>Vertical</SCROLLED>
            </PART>
            <LINE ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="MasterTypeStat">
              <FIELDS>NAME</FIELDS>
              <FIELDS>COUNT</FIELDS>
              <XMLTAG>MasterTypeStat</XMLTAG>
            </LINE>
            <FIELD ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="NAME">
              <SET>$NAME</SET>
              <XMLTAG>NAME</XMLTAG>
            </FIELD>
            <FIELD ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="COUNT">
              <SET>$StatVal</SET>
              <XMLTAG>COUNT</XMLTAG>
            </FIELD>
          </TDLMESSAGE>
        </TDL>
      </DESC>
    </BODY>
  </ENVELOPE> --}}
<!--
Generated Using TallyConnector - https://github.com/Accounting-Companion/TallyConnector
Incase of any errors raise a issue here - https://github.com/Accounting-Companion/TallyXmlsGenerator

-->
<!--
Generated Using TallyConnector - https://github.com/Accounting-Companion/TallyConnector
Incase of any errors raise a issue here - https://github.com/Accounting-Companion/TallyXmlsGenerator

-->
<!--
Generated Using TallyConnector - https://github.com/Accounting-Companion/TallyConnector
Incase of any errors raise a issue here - https://github.com/Accounting-Companion/TallyXmlsGenerator

-->
{{-- <ENVELOPE Action="">
    <HEADER>
      <VERSION>1</VERSION>
      <TALLYREQUEST>EXPORT</TALLYREQUEST>
      <TYPE>COLLECTION</TYPE>
      <ID>CUSTOMSTOCKGROUPCOL</ID>
    </HEADER>
    <BODY>
      <DESC>
        <STATICVARIABLES />
        <TDL>
          <TDLMESSAGE>
            <COLLECTION ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="CUSTOMSTOCKGROUPCOL">
              <TYPE>STOCKGROUP</TYPE>
            </COLLECTION>
          </TDLMESSAGE>
        </TDL>
    </DESC>
</BODY>
</ENVELOPE> --}}

<!--
Generated Using TallyConnector - https://github.com/Accounting-Companion/TallyConnector
Incase of any errors raise a issue here - https://github.com/Accounting-Companion/TallyXmlsGenerator

-->
{{-- <ENVELOPE Action="">
    <HEADER>
      <VERSION>1</VERSION>
      <TALLYREQUEST>EXPORT</TALLYREQUEST>
      <TYPE>COLLECTION</TYPE>
      <ID>CUSTOMUNITCOL</ID>
    </HEADER>
    <BODY>
      <DESC>
        <STATICVARIABLES />
        <TDL>
          <TDLMESSAGE>
            <COLLECTION ISMODIFY="No" ISFIXED="No" ISINITIALIZE="No" ISOPTION="No" ISINTERNAL="No" NAME="CUSTOMUNITCOL">
              <TYPE>UNIT</TYPE>
            </COLLECTION>
          </TDLMESSAGE>
        </TDL>
      </DESC>
    </BODY>
  </ENVELOPE> --}}
<!--
Generated Using TallyConnector - https://github.com/Accounting-Companion/TallyConnector
Incase of any errors raise a issue here - https://github.com/Accounting-Companion/TallyXmlsGenerator

-->
<!--
Generated Using TallyConnector - https://github.com/Accounting-Companion/TallyConnector
Incase of any errors raise a issue here - https://github.com/Accounting-Companion/TallyXmlsGenerator

--><ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>EXPORT</TALLYREQUEST>
        <TYPE>COLLECTION</TYPE>
        <ID>Filtered Ledgers</ID>
    </HEADER>
    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVFROMDATE>{{ $meta['YearStart'] }}</SVFROMDATE>
                <SVTODATE>{{ $meta['YearEnd'] }}</SVTODATE>
                <SVCURRENTCOMPANY>{{ $meta['CompanyName'] }}</SVCURRENTCOMPANY>
                <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
            </STATICVARIABLES>
            <TDL>
                <TDLMESSAGE>
                    <COLLECTION NAME="Filtered Ledgers" ISMODIFY="No">
                        <TYPE>Ledger</TYPE>
                        <FILTERS>LedgerNameStartsWithAtoM</FILTERS>
                        <FETCH>Name, Parent, ClosingBalance</FETCH>
                    </COLLECTION>
                    <SYSTEM TYPE="Formulae" NAME="LedgerNameStartsWithAtoM">
                        $$String:$Name:1 <= "M"
                    </SYSTEM>
                </TDLMESSAGE>
            </TDL>
        </DESC>
    </BODY>
</ENVELOPE>

