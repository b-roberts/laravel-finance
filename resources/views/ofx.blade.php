<?php echo '<?xml version="1.0"?>'; ?>

<ofx:OFX xmlns:ofx="http://ofx.net/types/2003/04">
    <SIGNONMSGSRSV1>
        <SONRS>
            <STATUS>
                <CODE>0</CODE>
                <SEVERITY>INFO</SEVERITY>
            </STATUS>
            <DTSERVER>{{date('YmdHis')}}</DTSERVER>
            <USERKEY>test</USERKEY>
            <TSKEYEXPIRE>{{date('YmdHis')}}</TSKEYEXPIRE>
            <LANGUAGE>ENG</LANGUAGE>
        </SONRS>
    </SIGNONMSGSRSV1>
    <BANKMSGSRSV1>
        @foreach($accounts as $account)
        <STMTTRNRS>
            <STATUS>
                <CODE>0</CODE>
                <SEVERITY>INFO</SEVERITY>
            </STATUS>
            <STMTRS>
                <CURDEF>USD</CURDEF>
                <BANKACCTFROM>
                    <BANKID>0</BANKID>
                    <ACCTID>XX{{substr($account->account_number,2)}}</ACCTID>
                    <ACCTTYPE>CHECKING</ACCTTYPE>
                </BANKACCTFROM>
                <BANKTRANLIST>
                    <DTSTART>20051001</DTSTART>
                    <DTEND>20051028</DTEND>
                    @foreach($account->transactions as $transaction)

                    <STMTTRN>
                        <TRNTYPE>OTHER</TRNTYPE>
                        <DTPOSTED>{{$transaction->date->format('YmdHis')}}</DTPOSTED>
                        <TRNAMT>{{$transaction->value *-1}}</TRNAMT>
                        <FITID>{{$transaction->fitid ?:0}}</FITID>
                        <SRVRTID>{{$transaction->id}}</SRVRTID>
                        <NAME>{{$transaction->location}}</NAME>
                        <MEMO>{{$transaction->note->description}}
                        <SPLTLST>
                            @foreach($transaction->categories as $category)
                            <SPLT>
                                <SPLTAMT>{{$category->pivot->value}}</SPLTAMT>
                                <SPLTNAME>{{$category->name}}</SPLTNAME>
                            </SPLT>
                            @endforeach
                        </SPLTLST>
                    </STMTTRN>
                    @endforeach

                </BANKTRANLIST>
                @php $ledgerBalance = $account->balances()->where('calculated',0)->orderBy('date','desc')->first(); @endphp
                <LEDGERBAL>
                    <BALAMT>{{$ledgerBalance->value}}</BALAMT>
                    <DTASOF>{{date('YmdHis',strtotime($ledgerBalance->date))}}</DTASOF>
                </LEDGERBAL>
                @foreach($account->balances()->where('calculated',0)->get() as $balance)

                <AVAILBAL>
                    <BALAMT>{{$balance->value}}</BALAMT>
                    <DTASOF>{{date('YmdHis',strtotime($balance->date))}}</DTASOF>
                </AVAILBAL>
                @endforeach
            </STMTRS>

        </STMTTRNRS>
@endforeach

    </BANKMSGSRSV1>
</ofx:OFX>
