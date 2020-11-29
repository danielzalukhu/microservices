const jwt = require('jsonwebtoken');

// Implementasi realnya gunakan token secretnya berupa sting dan yg susah ditebak
const JWT_SECRET = 'microservices123';

// 1. Create basic token dengan proses syncronous
/* <-- ################# --> */
// const token = jwt.sign({ 
//     data: { 
//         class: 'bwamicro' 
//     } 
// }, JWT_SECRET,
// { expiresIn: '1h' });
// console.log(token);

// 2. Create token dengan proses asyncronous
/* <-- ################# --> */
// Karena dia prosesnya ayncronous maka
// Kedua potongan kode dibawah ini
// yg akan dijalankan duluan adalah yg nge print AAA tanpa harus menunggu proses pembuatan token selesai
jwt.sign({ 
    data: { 
        class: 'bwamicro' } 
    }, JWT_SECRET, {
        expiresIn: '5m'
    }, (err, token) => {
        console.log(token)
});
// console.log('aaaaa');

// Verifikasi Token
const token1 = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImNsYXNzIjoiYndhbWljcm8ifSwiaWF0IjoxNjA2NjYwODQ0LCJleHAiOjE2MDY2NjExNDR9.i7a92kIt09WHNrANvH6asKl7x0ShMcRGwaCLztOUX-U';

// Cara 1
// jwt.verify(token1, JWT_SECRET, (err, decoded) => {
//     if (err) {
//         console.log(err.message);
//         return;
//     }
//     console.log(decoded);
// });

// Cara 2
try {
    const decoded = jwt.verify(token1, JWT_SECRET);
    console.log(decoded)
} catch (err) {
    console.log(err.message);
}

