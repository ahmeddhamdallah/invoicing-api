# Creating Sessions Example

```php
// Create a new session for a user
$user = User::find(1);
$session = $user->sessions()->create([
    'activated_at' => now(),  // If the user activated their session
    'appointment_at' => null  // No appointment yet
]);

// Later, when the user makes an appointment
$session->update([
    'appointment_at' => now()
]);

// Query user's sessions
$userSessions = $user->sessions()
    ->whereNotNull('activated_at')  // Get only activated sessions
    ->orWhereNotNull('appointment_at')  // Or sessions with appointments
    ->get();

// Check if user has any active sessions
$hasActiveSessions = $user->sessions()
    ->whereNotNull('activated_at')
    ->exists();

// Check if user has any appointments
$hasAppointments = $user->sessions()
    ->whereNotNull('appointment_at')
    ->exists();
```

The database structure ensures:
1. Each session belongs to exactly one user (through `user_id` foreign key)
2. When a user is deleted, all their sessions are automatically deleted (through `onDelete('cascade')`)
3. Both `activated_at` and `appointment_at` can be null, allowing sessions to track either activation, appointment, or both
4. Timestamps (`created_at` and `updated_at`) track when the session was created and last modified
