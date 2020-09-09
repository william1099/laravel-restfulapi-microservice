Hello {{ $user->name }} !
Thank you for signing in :)
Please verify your email by clicking the link below 

{{ route("user.verify", $user->verification_token) }}

