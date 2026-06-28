import { initializeApp } from "firebase/app";
import { getDatabase, ref, onValue, set, update } from "firebase/database";

const firebaseConfig = {
  apiKey: "AIzaSyDpDV7YAorXcNe0QXc0BIzxybZCqGPBRUA",
  authDomain: "piot-cdd47.firebaseapp.com",
  databaseURL: "https://piot-cdd47-default-rtdb.asia-southeast1.firebasedatabase.app",
  projectId: "piot-cdd47",
  storageBucket: "piot-cdd47.firebasestorage.app",
  messagingSenderId: "366500311831",
  appId: "1:366500311831:web:5f232444c2acc444befd52"
};

const app = initializeApp(firebaseConfig);
const database = getDatabase(app);

export { database, ref, onValue, set, update };
