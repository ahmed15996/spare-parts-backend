# Mobile Broadcasting Setup Guide

This guide explains how to set up real-time chat broadcasting for mobile apps using Laravel Sanctum authentication.

## 🔧 Backend Configuration

### 1. Broadcasting Routes
The backend now supports dual authentication:

- **Web Interface**: `/broadcasting/auth` (uses session cookies)
- **Mobile Apps**: `/api/broadcasting/auth` (uses Sanctum tokens)

### 2. Channel Authentication
Both web and mobile use the same channels:
- `user.{user_id}` - Private user channel
- `conversations.{conversation_id}` - Private conversation channel

### 3. Events
- **Event**: `MessageSent`
- **Channel**: `conversations.{conversation_id}` and `user.{user_id}`
- **Event Name**: `message.sent`

## 📱 Mobile App Integration

### For React Native with Pusher

```javascript
import Pusher from 'pusher-js/react-native';

// Initialize Pusher
const pusher = new Pusher('YOUR_PUSHER_APP_KEY', {
  cluster: 'YOUR_PUSHER_CLUSTER',
  authEndpoint: 'https://your-domain.com/api/broadcasting/auth',
  auth: {
    headers: {
      'Authorization': `Bearer ${userToken}`, // Sanctum token
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
  },
});

// Subscribe to user channel
const userChannel = pusher.subscribe(`private-user.${userId}`);
userChannel.bind('message.sent', (data) => {
  console.log('New message for user:', data);
  // Update UI with new message
});

// Subscribe to conversation channel
const conversationChannel = pusher.subscribe(`private-conversations.${conversationId}`);
conversationChannel.bind('message.sent', (data) => {
  console.log('New message in conversation:', data);
  // Update chat UI
});
```

### For Flutter with Pusher

```dart
import 'package:pusher_channels_flutter/pusher_channels_flutter.dart';

// Initialize Pusher
PusherChannelsFlutter pusher = PusherChannelsFlutter.getInstance();

await pusher.init(
  apiKey: 'YOUR_PUSHER_APP_KEY',
  cluster: 'YOUR_PUSHER_CLUSTER',
  onConnectionStateChange: onConnectionStateChange,
  onError: onError,
  onSubscriptionSucceeded: onSubscriptionSucceeded,
  onEvent: onEvent,
  onSubscriptionError: onSubscriptionError,
  onDecryptionFailure: onDecryptionFailure,
  onMemberAdded: onMemberAdded,
  onMemberRemoved: onMemberRemoved,
  onAuthorizer: onAuthorizer,
);

// Authorizer for Sanctum
dynamic onAuthorizer(String channelName, String socketId, dynamic options) {
  return {
    'Authorization': 'Bearer $userToken', // Sanctum token
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  };
}

// Subscribe to channels
await pusher.subscribe(channelName: 'private-user.$userId');
await pusher.subscribe(channelName: 'private-conversations.$conversationId');

// Handle events
void onEvent(PusherEvent event) {
  if (event.eventName == 'message.sent') {
    // Handle new message
    print('New message: ${event.data}');
  }
}
```

### For Native iOS with PusherSwift

```swift
import PusherSwift

// Initialize Pusher
let options = PusherClientOptions(
    authMethod: .authRequestBuilder(authRequestBuilder: AuthRequestBuilder())
)

let pusher = Pusher(key: "YOUR_PUSHER_APP_KEY", options: options)

// Auth Request Builder
class AuthRequestBuilder: AuthRequestBuilderProtocol {
    func requestFor(socketID: String, channelName: String) -> URLRequest? {
        var request = URLRequest(url: URL(string: "https://your-domain.com/api/broadcasting/auth")!)
        request.httpMethod = "POST"
        request.setValue("Bearer \(userToken)", forHTTPHeaderField: "Authorization")
        request.setValue("application/json", forHTTPHeaderField: "Accept")
        request.setValue("XMLHttpRequest", forHTTPHeaderField: "X-Requested-With")
        
        let params = "socket_id=\(socketID)&channel_name=\(channelName)"
        request.httpBody = params.data(using: .utf8)
        
        return request
    }
}

// Subscribe to channels
let userChannel = pusher.subscribe("private-user.\(userId)")
let conversationChannel = pusher.subscribe("private-conversations.\(conversationId)")

// Bind to events
userChannel.bind(eventName: "message.sent") { data in
    print("New message for user: \(data)")
}

conversationChannel.bind(eventName: "message.sent") { data in
    print("New message in conversation: \(data)")
}

pusher.connect()
```

### For Android with Pusher Java

```java
import com.pusher.client.Pusher;
import com.pusher.client.PusherOptions;
import com.pusher.client.util.HttpAuthorizer;

// Initialize Pusher
HttpAuthorizer authorizer = new HttpAuthorizer("https://your-domain.com/api/broadcasting/auth");
authorizer.setHeaders(Map.of(
    "Authorization", "Bearer " + userToken,
    "Accept", "application/json",
    "X-Requested-With", "XMLHttpRequest"
));

PusherOptions options = new PusherOptions()
    .setCluster("YOUR_PUSHER_CLUSTER")
    .setAuthorizer(authorizer);

Pusher pusher = new Pusher("YOUR_PUSHER_APP_KEY", options);

// Subscribe to channels
PrivateChannel userChannel = pusher.subscribePrivate("user." + userId);
PrivateChannel conversationChannel = pusher.subscribePrivate("conversations." + conversationId);

// Bind to events
userChannel.bind("message.sent", new PrivateChannelEventListener() {
    @Override
    public void onEvent(PusherEvent event) {
        System.out.println("New message for user: " + event.getData());
    }
});

conversationChannel.bind("message.sent", new PrivateChannelEventListener() {
    @Override
    public void onEvent(PusherEvent event) {
        System.out.println("New message in conversation: " + event.getData());
    }
});

pusher.connect();
```

## 🔑 Authentication Requirements

### For Mobile Apps:

1. **Get Sanctum Token**: Mobile app must first authenticate via `/api/v1/auth/login`
2. **Store Token**: Save the returned token securely
3. **Use Token**: Include `Authorization: Bearer {token}` in broadcasting auth headers
4. **Token Refresh**: Handle token expiration and refresh as needed

### Example Token Usage:

```javascript
// After login API call
const loginResponse = await fetch('https://your-domain.com/api/v1/auth/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password'
  })
});

const { token, user } = await loginResponse.json();

// Use this token for broadcasting authentication
const authHeaders = {
  'Authorization': `Bearer ${token}`,
  'Accept': 'application/json',
  'X-Requested-With': 'XMLHttpRequest',
};
```

## 🧪 Testing Broadcasting

### Test Endpoints:

1. **Check Auth Status**: `GET /debug-auth`
2. **Test Broadcasting**: `GET /test-broadcasting-auth`
3. **API Broadcasting**: `POST /api/broadcasting/auth`

### Message Event Structure:

```json
{
  "message": {
    "id": 1,
    "content": "Hello world!",
    "sender": {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe"
    },
    "conversation_id": 1,
    "created_at": "2024-01-01T12:00:00Z"
  }
}
```

## 🔒 Security Notes

1. **HTTPS Required**: Always use HTTPS in production
2. **Token Security**: Store Sanctum tokens securely on mobile devices
3. **Channel Authorization**: Users can only access conversations they're part of
4. **Rate Limiting**: Consider adding rate limiting to broadcasting endpoints

## 📝 Environment Variables

Make sure these are set in your `.env`:

```env
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster

VITE_PUSHER_APP_KEY=your_app_key
VITE_PUSHER_APP_CLUSTER=your_cluster

BROADCAST_CONNECTION=pusher
```

## 🚀 Next Steps for Mobile Developer

1. Choose your mobile platform and Pusher SDK
2. Implement authentication to get Sanctum token
3. Set up Pusher with the auth endpoint `/api/broadcasting/auth`
4. Subscribe to user and conversation channels
5. Handle incoming `message.sent` events
6. Test with the provided endpoints

For any issues, check the Laravel logs and use the debug endpoints to troubleshoot authentication problems.
