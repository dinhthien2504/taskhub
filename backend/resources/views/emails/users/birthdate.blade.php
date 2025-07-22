@component('mail::message')
# 🎂 Chúc mừng sinh nhật, {{ $name }}!

Chúng tôi xin gửi đến bạn những lời chúc tốt đẹp nhất trong ngày đặc biệt này.

@component('mail::panel')
Chúc bạn luôn vui vẻ, thành công và ngập tràn hạnh phúc!  
Cảm ơn bạn đã đồng hành cùng chúng tôi.
@endcomponent

@component('mail::button', ['url' => config('app.frontend_url')])
🎁 Ghé thăm chúng tôi
@endcomponent

---

Nếu bạn không muốn nhận email chúc mừng sinh nhật nữa, bạn có thể hủy đăng ký tại đây:

@component('mail::button', ['url' => config('app.frontend_url') . '/unsubscribe-birthdate?user_id=' . $userId])
🚫 Hủy nhận email sinh nhật
@endcomponent

Trân trọng,  
**Đội ngũ {{ config('app.name') }}**
@endcomponent
