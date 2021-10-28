const jwt = require("jsonwebtoken");
const JWT_SECRET = "X1adsf@&dfHAoil129!@#";

// Synchronous -> Basic create TOKEN -> return "STRING"
const token = jwt.sign({
        data: {
            kelas: "microservice",
            lecturer: "andy malarangeng",
        },
    },
    JWT_SECRET, {
        // Bisa "1h" (1 jam) atau 3600 (pakai detik tapi INTEGER)
        expiresIn: "1h",
    }
);

console.log("1. synchronnous ... " + token);

// Asynchronous -> Basic create TOKEN -> return "STRING"
jwt.sign({ data: { kelas: "micro", lecturer: "andyhong" } }, JWT_SECRET, { expiresIn: "5h" }, (err, token) => {
    console.log(token);
});

console.log("2. asynchronous");

// Verifikasi TOKEN

const token1 = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImtlbGFzIjoibWljcm9zZXJ2aWNlIiwibGVjdHVyZXIiOiJhbmR5IG1hbGFyYW5nZW5nIn0sImlhdCI6MTYyOTc4MDQyMCwiZXhwIjoxNjI5Nzg0MDIwfQ.DQsYrILwplLW5IhByuVHMmrOAyXTw3sQtYAv1k-XE3g";

// Cara 1

jwt.verify(token1, JWT_SECRET, (err, decoded) => {
    if (err) {
        console.log(err.message);
        return;
    }

    console.log(decoded);
});

// Cara 2
try {
    const decode = jwt.verify(token1, JWT_SECRET);
    console.log(decode);
} catch (error) {
    console.log(error.message);
}