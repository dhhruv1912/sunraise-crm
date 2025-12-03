  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.10.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.10.0/firebase-analytics.js";
  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  const firebaseConfig = {
    apiKey: "AIzaSyCmp373gcRH25Ibefu0je8e5FCn19qutsw",
    authDomain: "sunraise-cfe57.firebaseapp.com",
    projectId: "sunraise-cfe57",
    storageBucket: "sunraise-cfe57.appspot.com",
    messagingSenderId: "369245736590",
    appId: "1:369245736590:web:9a23bf0a2bcdfa2f75c3df",
    measurementId: "G-44V6PR6374"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
