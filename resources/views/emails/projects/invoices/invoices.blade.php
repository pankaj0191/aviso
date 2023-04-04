@extends('email')
@section('content')
<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="margin-bottom: 0px;">
    <tr>
        <td
            style="padding: 40px; padding-bottom: 0px; font-family: sans-serif; font-size: 20px; line-height: 27px; color: #666666; font-size: 16px">
            {{-- Hi {{$name}}, --}}
        </td>
    </tr>
    <tr>
        <td
            style="padding: 40px; padding-top: 10px; font-family: sans-serif; font-size: 20px; line-height: 27px; color: #666666; font-size: 14px; text-align: justify">
            <div style="text-align:center; margin-bottom:24px;font-size:24px;font-weight:900">
                Prooflo Invoice
            </div>
            <div style="text-align:right;margin-bottom:16px">
                <div>Balance due</div>
                <div style="font-weight: 700; font-size:24px">$35</div>
            </div>
            <table class="table" cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" align="center" style="margin-bottom: 0px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project Name</th>
                        <th>Rate</th>
                        <th>Duration</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <td>1</td>
                        <td class="color-item">Nike App</td>
                        <td>$20</td>
                        <td>02:00</td>
                        <td>$40</td>
                    </tr>
                </thead>
            </table>
        </td>
    </tr>
    <tr>
    <tr>
        <td
            style="padding: 40px; padding-top: 10px; font-family: sans-serif; font-size: 20px; line-height: 27px; color: #666666; font-size: 14px; text-align: justify">
            <span>Subtotal:</span> <b>$40</b><br>
            <span>Prooflo Tax:</span> <b>$40</b><br>
            <span>Balance due:</span> <b>$40</b>
        </td>
    </tr>
    <tr>
        <td valign="middle" align="center"
            style="-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background-image: url(http://tedgoas.github.io/Cerberus/assets/bg-btn.png); background-position: top left; background-repeat: repeat-x; background-color: #fff; padding-bottom: 30px">
            <a href="#" target="blank"
                style="color: #ffffff; font-family: sans-serif; font-size: 15px; line-height: 15px; text-align: center; text-decoration: none; display: block; padding: 15px 20px; border: 1px solid #333333; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background-color: #575959; width: 130px; border: 0px">
                <b>Donwload PDF</b>
            </a>
        </td>
    </tr>
    <tr>
        <td valign="middle" align="center" style="padding: 0 0 25px 0;">
            <a href="{{config('app.url')}}/settings/notifications" target="blank"
                style="text-decoration: none; font-size: 11px; color: #80b4a0;">
                <b>Unsubscribe from Prooflo Emails</b>
            </a>
        </td>
    </tr>
</table>
@endsection
