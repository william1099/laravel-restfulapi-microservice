Hello {{ $user->name }} !
You changed your email.
Please confirm your email by clicking the link below 

{{ route("user.verify", $user->verification_token) }}

