<table>
    <thead>
        <tr>
            <th colspan="4" rowspan="2" style="background-color: yellow;">
                Attandance Report Of Month {{ $range }}
                <br>
                For {{ $user_name }}
            </th>
        </tr>
        <tr>
        </tr>
        <tr>
            <th>Date</th>
            <th>In</th>
            <th>Out</th>
            <th>Hours</th>
        </tr>
    </thead>
    <tbody>
        @for ($i = 01; $i < 32; $i++)
            @php
                $style = "";
                $dateTime = new DateTime($date_."-".sprintf('%02d', $i));
                $dayOfWeek = $dateTime->format('l');  // Returns the day of the week (e.g., 'Saturday')
            @endphp
            @if(in_array($dayOfWeek,['Sunday']))
                @php
                    $style .= "background-color: yellow;"
                @endphp
            @endif
            @if (isset($at[$date_."-".sprintf('%02d', $i)]))
                @php
                    $d = $at[$date_."-".sprintf('%02d', $i)];
                @endphp
                <tr style="{{ $style }}">
                    <td>{{ $date_."-".sprintf('%02d', $i) }}</td>
                    <td>{{ $d['in'] }}</td>
                    <td>{{ $d['out'] }}</td>
                    <td>{{ $d['hours'] }}</td>
                </tr>
            @else
                <tr style="{{ $style }}">
                    <td>{{ $date_."-".sprintf('%02d', $i) }}</td>
                    <td>--</td>
                    <td>--</td>
                    <td>0:00</td>
                </tr>
            @endif
        @endfor
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="3">Total Working Hours :</td>
            <td>{{ round($working_minutes / 60) }}</td>
        </tr>
        <tr>
            <td colspan="3">Total Worked Hours :</td>
            <td>{{ round($worked_minutes / 60) }}</td>
        </tr>
        <tr>
            <td colspan="3">Total Payable Salary for {{ round($worked_minutes / 60) }} Hours :</td>
            <td>₹{{ $payable_salary }}</td>
        </tr>
    </tbody>
</table>
