<h2>⚠️ Import người dùng thất bại</h2>
<p>Một số dòng trong file CSV không hợp lệ và không thể import:</p>

<table border="1" cellpadding="6" cellspacing="0">
    <thead>
        <tr>
            <th>Dòng</th>
            <th>Dữ liệu</th>
            <th>Lỗi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($failedRows as $error)
            <tr>
                <td>{{ $error['row'] }}</td>
                <td>
                    <pre>{{ json_encode($error['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </td>
                <td>
                    <ul>
                        @foreach ($error['errors'] as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
