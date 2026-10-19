<table dir="ltr">
    <tbody>
        <tr>
            <td style="padding:0 0 20px 0;">
                <img src="{{ $message->embed(storage_path('app/public/images/logo.png')) }}" width="280" alt="{{ $NAMA_SEKOLAH }}" style="display:block;border:0;width:280px;max-width:100%;height:auto;">
            </td>
        </tr>
        <tr>
            <td style="padding:0;font-family:'Segoe UI Semibold','Segoe UI Bold','Segoe UI','Helvetica Neue Medium',Arial,sans-serif;font-size:17px;color:#707070">
                {{ $NAMA_SEKOLAH }}
            </td>
        </tr>
        <tr>
            <td style="padding:0;font-family:'Segoe UI Light','Segoe UI','Helvetica Neue Medium',Arial,sans-serif;font-size:41px;color:#2672ec">
                Reset Password
            </td>
        </tr>
        <tr>
            <td style="padding:0;padding-top:25px;font-family:'Segoe UI',Tahoma,Verdana,Arial,sans-serif;font-size:14px;color:#2a2a2a">
                Kami menerima permintaan untuk mengatur ulang password akun atas nama {{ $name }}.
            </td>
        </tr>
        <tr>
            <td style="padding:0;padding-top:25px;font-family:'Segoe UI',Tahoma,Verdana,Arial,sans-serif;font-size:14px;color:#2a2a2a">
                <a href="{{ $resetUrl }}" style="display:inline-block;padding:12px 18px;background:#2672ec;color:#ffffff;text-decoration:none;font-family:'Segoe UI',Tahoma,Verdana,Arial,sans-serif;font-size:14px;">
                    Atur Password Baru
                </a>
            </td>
        </tr>
        <tr>
            <td style="padding:0;padding-top:25px;font-family:'Segoe UI',Tahoma,Verdana,Arial,sans-serif;font-size:14px;color:#2a2a2a">
                Atau salin tautan berikut ke peramban:
                <br>
                <a href="{{ $resetUrl }}" style="color:#2672ec">{{ $resetUrl }}</a>
            </td>
        </tr>
        <tr>
            <td style="padding:0;padding-top:25px;font-family:'Segoe UI',Tahoma,Verdana,Arial,sans-serif;font-size:14px;color:#2a2a2a">
                Tautan ini berlaku 60 menit dan hanya dapat dipakai satu kali.
            </td>
        </tr>
        <tr>
            <td style="padding:0;padding-top:25px;font-family:'Segoe UI',Tahoma,Verdana,Arial,sans-serif;font-size:14px;color:#2a2a2a">
                Abaikan email ini jika Anda tidak meminta reset password.
            </td>
        </tr>
        <tr>
            <td style="padding:0;padding-top:25px;font-family:'Segoe UI',Tahoma,Verdana,Arial,sans-serif;font-size:14px;color:#2a2a2a">
                Terimakasih,
            </td>
        </tr>
        <tr>
            <td style="padding:0;font-family:'Segoe UI',Tahoma,Verdana,Arial,sans-serif;font-size:14px;color:#2a2a2a">
                <span>TIM</span> IT {{ $NAMA_SEKOLAH }}
            </td>
        </tr>
    </tbody>
</table>
