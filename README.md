# 🚀 FCM Push Notification Sender (Core PHP)

A simple and lightweight Firebase Cloud Messaging (FCM) push notification sender built with **Core PHP (no external libraries)**.

This project demonstrates how to:

* Generate OAuth 2.0 access token using a Firebase service account
* Send push notifications via FCM HTTP v1 API
* Use topic-based messaging
* Build a simple web form for sending notifications

---

## 📸 Features

* 🔐 OAuth 2.0 Access Token (JWT based)
* 📲 Send notifications to topic (`All_FCM`)
* 🧾 Simple UI form (Title & Body input)
* ⚡ No Composer / No external dependencies
* 🧠 Clean and reusable code

---

## 📁 Project Structure

```
pushNotification/
├── index.php
└── service-account.json
```

---

## 🔑 Setup Instructions

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/your-username/pushNotification.git
cd pushNotification
```

---

### 2️⃣ Download Service Account Key

Go to Firebase Console: https://console.firebase.google.com/

* Select your project
* Go to **Project Settings → Service Accounts**
* Click **Generate new private key**
* Download JSON file

Rename it:

```
service-account.json
```

Place it in the project root folder.

---

## ▶️ Run the Project

Using XAMPP or any PHP server:

```
http://localhost/pushNotification/index.php
```

---

## 🧪 How It Works

1. Enter notification **Title**
2. Enter notification **Body**
3. Click **Send**
4. Notification will be delivered to all users subscribed to:

---

## 📱 Android Setup (Important)

Make sure your Android app subscribes to the topic:

```java
FirebaseMessaging.getInstance().subscribeToTopic("topic_name");
```

---

## 📡 API Used

* Firebase Cloud Messaging HTTP v1 API
* OAuth 2.0 JWT Authentication

---

## ⚡ Example Payload

```json
{
  "message": {
    "topic": "topic_name",
    "notification": {
      "title": " Notice 🚀",
      "body": "সকল ব্যবহারকারীদের জানানো যাচ্ছে যে, সবাই তাদের unsync data দ্রুত sync করে নিন।"
    },
    "data": {
      "type": "type",
      "id": "12345"
    },
    "android": {
      "priority": "high",
      "notification": {
        "sound": "default"
       
      }
    }
  }
}
}
