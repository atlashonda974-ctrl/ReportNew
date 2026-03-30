<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background: #f4f4f4; }
        .email-container { max-width: 750px; margin: 0 auto; background: #fff; border: 1px solid #ddd; border-radius: 5px; overflow: hidden; }
        .email-header { background: #007bff; color: #fff; padding: 20px; text-align: center; }
        .email-body { padding: 20px; }
        .email-footer { background: #f8f9fa; padding: 15px 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; text-align: center; }
        .pre-formatted { white-space: pre-line; font-family: inherit; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
        th { background: #007bff; color: #fff; padding: 8px 10px; text-align: left; }
        td { padding: 7px 10px; border-bottom: 1px solid #eee; vertical-align: middle; }
        tr:nth-child(even) td { background: #f9f9f9; }
        .attach-link {
            display: inline-block; background: #0d6efd; color: #fff !important;
            font-size: 12px; padding: 4px 12px; border-radius: 4px;
            font-weight: bold; text-decoration: none;
        }
        .no-attach { color: #aaa; font-size: 12px; }
    </style>
</head>
<body>
<div class="email-container">

    <div class="email-header">
        <h2 style="margin:0;">Atlas Insurance Ltd.</h2>
        <p style="margin:8px 0 0 0;">Reinsurance Request Note</p>
    </div>

    <div class="email-body">

        {{-- Email body text --}}
        <div class="pre-formatted">{!! $content !!}</div>

        {{-- Records table --}}
        @if(!empty($records))
        <br>
        <strong>Selected Records:</strong>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Request Note</th>
                    <th>Insured</th>
                    <th>Dept</th>
                    <th>Doc Date</th>
                    
                </tr>
            </thead>

            <tbody>
                @foreach($records as $i => $rec)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $rec['reqNote'] ?? 'N/A' }}</strong></td>
                    <td>{{ $rec['insured'] ?? 'N/A' }}</td>
                    <td>{{ $rec['dept'] ?? 'N/A' }}</td>
                    <td>{{ $rec['docDate'] ?? 'N/A' }}</td>
                    
                </tr>
                @endforeach
            </tbody>

        </table>
        @endif

    </div>

    <div class="email-footer">
        <strong>Atlas Insurance Ltd.</strong><br>
        This is an automated email. Please do not reply.
    </div>

</div>
</body>
</html>