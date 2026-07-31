<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
</head>
<body style="margin:0;background:#f7f9ee;color:#1f2a16;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f7f9ee;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:760px;background:#ffffff;border:1px solid #dfe6c4;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background:#415014;color:#ffffff;padding:22px 26px;">
                            <div style="font-size:22px;font-weight:800;line-height:1.2;">{{ $brandName ?? config('app.name') }}</div>
                            <div style="margin-top:5px;color:#fff8ba;font-size:13px;">@yield('preheader', 'Store notification')</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:26px;">
                            @yield('content')
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 26px;background:#fffbc8;border-top:1px solid #e7e0a0;color:#415014;font-size:13px;line-height:1.6;">
                            If you have any questions, feel free to contact us.<br>
                            Email: <a href="mailto:{{ config('mail.from.address') }}" style="color:#415014;text-decoration:none;">{{ config('mail.from.address') }}</a><br>
                            Regards,<br>
                            <strong>{{ $brandName ?? config('app.name') }}</strong>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
