<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Slip</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }
        .box {
            border: 1px solid #ccc;
            padding: 16px;
        }
        h2 {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background: #f5f5f5;
            text-align: left;
        }
        .right {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Salary Slip</h2>
    <p><strong>Month:</strong> {{ $month }}</p>

    <hr>

    <p>
        <strong>Employee:</strong>
        {{ $user->fname }} {{ $user->lname }}
    </p>

    <table>
        <tr>
            <th>Total Working Days</th>
            <td class="right">{{ $workingDays }}</td>
        </tr>
        <tr>
            <th>Working Minutes</th>
            <td class="right">{{ $workingMinutes }}</td>
        </tr>
        <tr>
            <th>Worked Minutes</th>
            <td class="right">{{ $workedMinutes }}</td>
        </tr>
        <tr>
            <th>Monthly Salary</th>
            <td class="right">₹ {{ number_format($salary, 2) }}</td>
        </tr>
        <tr>
            <th>Payable Salary</th>
            <td class="right">
                <strong>₹ {{ number_format($payableSalary, 2) }}</strong>
            </td>
        </tr>
    </table>

    <p style="margin-top:40px">
        <em>This is a system generated salary slip.</em>
    </p>
</div>

</body>
</html>
