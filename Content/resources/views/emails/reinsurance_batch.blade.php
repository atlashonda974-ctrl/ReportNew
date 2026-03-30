<p>{{ $body }}</p>

<table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>Req Note</th>
            <th>Insured</th>
            <th>Dept</th>
            <th>RI Sum Insured</th>
            <th>Expiry Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $item)
            <tr>
                <td>{{ $item['reqNote'] }}</td>
                <td>{{ $item['insured'] }}</td>
                <td>{{ $item['dept'] }}</td>
                <td>{{ $item['riSi'] }}</td>
                <td>{{ $item['expiryDate'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p>Regards,<br>Reinsurance Department</p>